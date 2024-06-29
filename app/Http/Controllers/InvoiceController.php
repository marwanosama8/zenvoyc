<?php

namespace App\Http\Controllers;

use App\Mail\Invoice\MailInvoice as InvoiceMailInvoice;
use App\Mail\Invoice\MailReminder as InvoiceMailReminder;
use App\Mail\MailInvoice;
use App\Mail\MailReminder;
use App\Mapper\InvoiceDataMapper;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Scopes\InvoiceScope;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Knp\Snappy\Pdf;
use Spatie\Browsershot\Browsershot;

class InvoiceController extends Controller
{
    public function __construct(
        private InvoiceDataMapper $invoiceDataMapper,
    ) {
    }


    public function view($invoice)
    {
        $invoice = Invoice::withoutGlobalScope(InvoiceScope::class)->with('customer')->whereInvoiceNumber($invoice)->first();
        $data = $this->invoiceDataMapper->getData($invoice);
        // dd($data);
        abort_if(!$this->isInvoiceAccessable($invoice), 401);
        return view('invoice.view', ['data' => $data, 'print' => 0]);
    }

    public function generate($invoice)
    {
        $invoice = Invoice::withoutGlobalScope(InvoiceScope::class)->whereInvoiceNumber($invoice)->first();
        $info = $this->invoiceDataMapper->getData($invoice);

        $data = ['data' => $info, 'print' => 1];
        $content = view('invoice.view', ['data' => $info, 'print' => 1])->render();
        $filename = 'RG ' . $invoice->rgnr . ' ' . $info['invoice']->customer->name . '.pdf';
        // $wkhtml2pdf = App::make('snappy.pdf');
        $wkhtml2pdf = new Pdf('/usr/local/bin/wkhtmltopdf');
        #dd(storage_path());
        // dd($wkhtml2pdf->setTemporaryFolder(storage_path() . '/tmp'));


        #return $wkhtml2pdf->getOutputFromHtml($content, []);
        // dd($wkhtml2pdf->generateFromHtml($content, $filename));
        return $wkhtml2pdf->generateFromHtml($content, $filename);
        //return ['file' => $wkhtml2pdf->getOutputFromHtml($content), 'filename' => $filename ];
    }



    public function download($invoice)
    {
        if (!$invoice instanceof Collection) {
            $invoice = Invoice::withoutGlobalScope(InvoiceScope::class)->whereInvoiceNumber($invoice)->first();
        }
        $pdf = $this->storePdfFile($invoice);
        return  response()->download($pdf['path']);
    }

    public function send($invoice)
    {
        $invoice = Invoice::withoutGlobalScope(InvoiceScope::class)->whereInvoiceNumber($invoice)->first();
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
        return $this->view($invoice->invoice_number);
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
        return $this->view($invoice->invoice_number);
    }


    public function reminder($invoice)
    {
        $invoice = Invoice::withoutGlobalScope(InvoiceScope::class)->whereId($invoice)->first();

        $fileData = [
            'file' => $this->generatePdfFile($invoice)->pdf(),
            'filename' => $this->generateFileName($invoice)
        ];
        Mail::to($invoice->customer->email)->send(new InvoiceMailReminder($invoice, $fileData));
        return redirect()->back();
    }

    // new
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
            ->paperSize(280, 330)
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

        if (Storage::exists($foldername)) {
            return [
                'pdf' => $pdf,
                'path' => $invoiceMedia->path
            ];
        }

        Storage::put($foldername . $filename, $pdf->pdf());

        $invoice->update([
            'printed' => 1
        ]);

        return [
            'pdf' => $pdf,
            'path' => $invoiceMedia->path
        ];
    }

    public function duplicate(Invoice $invoice)
    {
        $oldInvoice = $invoice;
        $newInvoice = $oldInvoice->replicate();
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
