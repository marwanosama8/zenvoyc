<?php

namespace App\Http\Controllers;

use App\Mail\MailInvoice;
use App\Mail\MailReminder;
use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Knp\Snappy\Pdf;
use Spatie\Browsershot\Browsershot;

class InvoiceController extends Controller
{
    public function view(Invoice $invoice, Request $request)
    {
        // dd($invoice);
        return view('invoice.view', ['data' => $invoice, 'request' => $request, 'print' => 0]);
    }

    public function generate(Invoice $invoice, Request $request)
    {

        $data = ['data' => $invoice, 'request' => $request, 'print' => 1];
        $content = view('invoice.view', ['data' => $invoice, 'request' => $request, 'print' => 1])->render();
        $filename = 'RG ' . $invoice->rgnr . ' ' . $invoice->Customer()->first()->name . '.pdf';
        // $wkhtml2pdf = App::make('snappy.pdf');
        $wkhtml2pdf = new Pdf('/usr/local/bin/wkhtmltopdf');
        #dd(storage_path());
        // dd($wkhtml2pdf->setTemporaryFolder(storage_path() . '/tmp'));


        #return $wkhtml2pdf->getOutputFromHtml($content, []);
        // dd($wkhtml2pdf->generateFromHtml($content, $filename));
        return $wkhtml2pdf->generateFromHtml($content, $filename);
        //return ['file' => $wkhtml2pdf->getOutputFromHtml($content), 'filename' => $filename ];
    }



    public function download(Invoice $invoice, Request $request)
    {
        $pdf = $this->storePdfFile($invoice, $request);
        return  response()->download($pdf['path']);
    }

    public function send(Invoice $invoice, Request $request)
    {
        $this->download($invoice, $request);
        if (!$invoice->send) {
            $fileData = [
                'file' => $this->generatePdfFile($invoice, $request)->pdf(),
                'filename' => $this->generateFileName($invoice)
            ];
            $sendmail = Mail::to($invoice->customer->email);
            if ($invoice->customer->cc != '') {
                $sendmail->cc($invoice->customer->cc);
            }
            $sendmail->send(new MailInvoice($invoice, $fileData));
            $invoice->printed = 1;
            $invoice->send = 1;
            $invoice->save();
        } else {
            $request->request->add(['notification' => ['type' => 'danger', 'message' => 'Mail wurde bereits versand - zum erneuten senden <a href="' . route('invoice.resend', $invoice->id) . '">hier klicken</a>']]);
        }
        return $this->view($invoice, $request);
    }

    public function resend(Invoice $invoice, Request $request)
    {
        $fileData = $this->generate($invoice, $request);
        $sendmail = Mail::to($invoice->Customer->email);
        if ($invoice->Customer->cc != '') {
            $sendmail->cc($invoice->Customer->cc);
        }
        $sendmail->send(new MailInvoice($invoice, $fileData));
        $request->request->add(['notification' => ['type' => 'success', 'message' => 'Mail wurde erneut versand']]);
        return $this->view($invoice, $request);
    }


    public function reminder(Invoice $invoice, Request $request)
    {
        $fileData = [
            'file' => $this->generatePdfFile($invoice, $request)->pdf(),
            'filename' => $this->generateFileName($invoice)
        ];
        Mail::to($invoice->customer->email)->send(new MailReminder($invoice, $fileData));
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

    protected function generatePdfContent($invoice, $request)
    {
        $data = ['data' => $invoice, 'request' => $request, 'print' => 1];
        return view('invoice.view', $data)->render();
    }

    protected function generatePdfFile($invoice, $request)
    {
        $content = $this->generatePdfContent($invoice, $request);
        return Browsershot::html($content)
            ->showBackground()
            ->waitUntilNetworkIdle()
            ->paperSize(280, 330)
            ->ignoreHttpsErrors();
    }

    protected function storePdfFile($invoice, $request)
    {
        $foldername = $this->generateFolderName($invoice);
        $filename = $this->generateFileName($invoice);

        $content = $this->generatePdfContent($invoice, $request);

        $invoiceMedia = $invoice->invoiceMedia()->create([
            'path' => str_replace('public', 'storage', $foldername) . $filename,
            'content' => $content
        ]);

        $pdf = $this->generatePdfFile($invoice, $request);

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
}
