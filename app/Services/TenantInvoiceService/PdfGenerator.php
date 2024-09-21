<?php

namespace App\Services\TenantInvoiceService;

use App\Constants\FakeInvoiceData;
use App\Jobs\SendReminderEmailJob;
use App\Mail\TenantInvoice\MailReminder;
use App\Mapper\InvoiceDataMapper;
use App\Models\Scopes\TenantInvoiceScope;
use App\Models\TenantInvoice;
use App\Services\TenantInoiceService\TenantInvoiceInterface;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Str;

class PdfGenerator
{
    public function __construct(
        private InvoiceDataMapper $invoiceDataMapper
    ) {}

    public function temporary($invoiceRgnr)
    {
        $uuid = Str::uuid();
        $temporaryDirectory = (new TemporaryDirectory())->create();
        $data = $this->getData($invoiceRgnr);
        $pdfPath = $temporaryDirectory->path("{$uuid}.pdf");

        $pdfContent = Pdf::loadView('invoice.new-view', ['data' => $data, 'print' => 1])->output();
        Storage::disk('local')->put($pdfPath, $pdfContent);

        return $pdfPath;
    }

    public function remind($rgnr)
    {
        $data = $this->getData($rgnr);
        $fileData = [
            'file' => Pdf::loadView('invoice.new-view', ['data' => $data, 'print' => 1])->output(),
            'filename' => $this->generateFileName($data['invoice'])
        ];

        SendReminderEmailJob::dispatch($data);
        // Mail::to($data['invoice']->customer->email)->send(new MailReminder($data['provider'], $data['invoice'], $fileData));
        return redirect()->back();
    }

    public function stream($invoiceRgnr)
    {
        $data = $this->getData($invoiceRgnr);
        return Pdf::loadView('invoice.new-view', ['data' => $data, 'print' => 1])->stream($this->generateFileName($data['invoice']));
    }

    public function getData($rgnr)
    {
        $invoice = TenantInvoice::withoutGlobalScope(TenantInvoiceScope::class)->with('customer')->whereRgnr($rgnr)->first();
        return  $this->invoiceDataMapper->getData($invoice);
    }

    public function generateFileName($invoice): string
    {
        return "RG {$invoice->rgnr} {$invoice->customer->name}.pdf";
    }

    public function generateFakePdf($type)
    {

        $invoiceData = new FakeInvoiceData($type);
        
        $data = $invoiceData->getData();

        $pdf = Pdf::loadView('invoice.new-view', ['data' => $data, 'print' => 1]);

        return $pdf->stream($this->generateFileName($data['invoice']));
    }
}
