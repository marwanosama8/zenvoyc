<?php

namespace App\Http\Controllers;

use App\Mail\Invoice\MailInvoice as InvoiceMailInvoice;
use App\Mail\Invoice\MailReminder as InvoiceMailReminder;
use App\Mapper\InvoiceDataMapper;
use App\Models\Company;
use App\Models\TenantInvoice as Invoice;
use App\Models\Scopes\TenantInvoiceScope as InvoiceScope;
use App\Services\TenantInvoiceService\PdfGenerator;
use App\Services\TenantInvoiceService\XmlGenerator;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
// use Knp\Snappy\Pdf;
use Spatie\Browsershot\Browsershot;
use horstoeko\zugferd\ZugferdDocumentPdfMerger;
use Illuminate\Support\Facades\Log;
use Spatie\TemporaryDirectory\TemporaryDirectory;

class TenantInvoiceController extends Controller
{
    public function __construct(
        private XmlGenerator $xmlGenerator,
        private PdfGenerator $pdfGenerator,
        private InvoiceDataMapper $invoiceDataMapper

    ) {}


    public function view($rgnr)
    {
        try {
            $data = $this->pdfGenerator->getData($rgnr);
        } catch (\Exception $th) {
            Log::error($th->getMessage());
            abort(401);
        }

        return view('invoice.new-view', ['data' => $data, 'print' => 0]);
    }

    public function streamPdfInvoice($rgnr)
    {
        return $this->pdfGenerator->stream($rgnr);
    }

    public function mergeWithPdf($rgnr, $profile)
    {
        $xmlPath = $this->xmlGenerator->temporaryGenerate($rgnr, $profile);
        $pdfPath = $this->pdfGenerator->temporary($rgnr);
        $data = $this->pdfGenerator->getData($rgnr);
        $filename = $this->pdfGenerator->generateFileName($data['invoice']);
        $mergedPdfPath = $this->mergeXmlAndPdf($xmlPath, $pdfPath, $filename);

        return response()->download($mergedPdfPath);
    }
    private function mergeXmlAndPdf($xmlPath, $pdfPath, $filename)
    {
        $temporaryDirectory = (new TemporaryDirectory())->create();
        $mergeToPdf = $temporaryDirectory->path($filename);

        (new ZugferdDocumentPdfMerger(storage_path('app' . $xmlPath), storage_path('app' . $pdfPath)))
            ->generateDocument()
            ->saveDocument($mergeToPdf);

        return $mergeToPdf;
    }

    public function xmlDownload($rgnr, $profile)
    {
        return $this->xmlGenerator->download($rgnr, $profile);
    }

    public function createFakeInvoice($type)
    {
        return $this->pdfGenerator->generateFakePdf($type);
    }

    public function reminder($rgnr)
    {
        return $this->pdfGenerator->remind($rgnr);
    }

    public function customerInvoices($token)
    {
        return view('invoice.customer-invoices', ['token' => $token]);
    }

    // old
    public function download($invoice)
    {
        if (!$invoice instanceof Collection) {
            $invoice = Invoice::withoutGlobalScope(InvoiceScope::class)->whereRgnr($invoice)->first();
        }
        $pdf = $this->storePdfFile($invoice);
        return  response()->download($pdf['path']);
    }

    public function send($rgnr)
    {
        $invoice = Invoice::withoutGlobalScope(InvoiceScope::class)->whereRgnr($rgnr)->first();
        // dd($invoice);

        $this->download($invoice);
        if (!$invoice->send) {
            $fileData = [
                'file' => $this->generatePdfFile($invoice)->pdf(),
                'filename' => $this->generateFileName($invoice)
            ];
            $sendmail = Mail::to($invoice->customer->email);
            if ($invoice->customer->cc != '') {
                $sendmail->cc($invoice->customer->cc);
            }
            $sendmail->send(new InvoiceMailInvoice($invoice, $fileData));
            $invoice->printed = 1;
            $invoice->send = 1;
            $invoice->save();
        } else {
        }
        return $this->view($invoice->rgnr);
    }

    public function resend($invoice)
    {
        $invoice = Invoice::withoutGlobalScope(InvoiceScope::class)->whereId($invoice)->first();

        $fileData = $this->generate($invoice,);
        $sendmail = Mail::to($invoice->Customer->email);
        if ($invoice->Customer->cc != '') {
            $sendmail->cc($invoice->Customer->cc);
        }
        $sendmail->send(new InvoiceMailInvoice($invoice, $fileData));
        return $this->view($invoice->rgnr);
    }

    protected function generateFileName($invoice)
    {
        return "RG {$invoice->rgnr} {$invoice->customer->name}.pdf";
    }

    protected function generateFolderName($invoice)
    {
        return "public/pdf/{$invoice->id}/" . Carbon::now()->format('d.m.Y') . '/';
    }

    protected function generatePdfContent($invoice)
    {
        $content = $this->invoiceDataMapper->getData($invoice);
        $data = ['data' => $content, 'print' => 1];
        return view('invoice.view', $data)->render();
    }

    protected function generatePdfFile($invoice)
    {
        $content = $this->generatePdfContent($invoice);
        return Browsershot::html($content)
            ->showBackground()
            ->waitUntilNetworkIdle()
            ->paperSize(300, 330)
            ->ignoreHttpsErrors();
    }

    protected function storePdfFile($invoice)
    {
        $foldername = $this->generateFolderName($invoice);
        $filename = $this->generateFileName($invoice);

        $content = $this->generatePdfContent($invoice);

        $invoiceMedia = $invoice->invoiceMedia()->create([
            'path' => str_replace('public', 'storage', $foldername) . $filename,
            'content' => $content
        ]);

        $pdf = $this->generatePdfFile($invoice);

        // if (Storage::exists($foldername)) {
        //     return [
        //         'pdf' => $pdf,
        //         'path' => $invoiceMedia->path
        //     ];
        // }

        Storage::put($foldername . $filename, $pdf->pdf());

        $invoice->update([
            'printed' => 1
        ]);

        return [
            'pdf' => $pdf,
            'path' => $invoiceMedia->path
        ];
    }

    public function duplicate($rgnr)
    {
        $oldInvoice = Invoice::withoutGlobalScope(InvoiceScope::class)->whereRgnr($rgnr)->first();
        $newInvoice = $oldInvoice->replicate();
        $newInvoice->rgnr = Invoice::getNextNr();
        $newInvoice->save();

        $oldItems = $oldInvoice->InvoiceItem;

        foreach ($oldItems as $oldItem) {
            $newItem = $oldItem->replicate();
            $newItem->invoice_id = $newInvoice->id;
            $newItem->save();
        }

        return redirect()->back();
    }

    private function isInvoiceAccessable($invoice)
    {
        $tenantId = session()->get('tenant_id');
        $user = auth()->user();
        if (is_null($tenantId)) {
            // this is a user
            // is this invoice realted to this auth user ot not
            return $invoice->invoiceable_type == 'App\Models\User' && $invoice->invoiceable_id == $user->id;
        } else {
            // this is company 
            $tenant = Company::find($tenantId);
            // if this invoice realted to this auth user, plus this tenet ot not 
            return $invoice->invoiceable_type == 'App\Models\Company' && $invoice->invoiceable_id == $tenant->id;
        }
    }
}
