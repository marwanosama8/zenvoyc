<?php

namespace App\Services\TenantInvoiceService;

use App\Constants\FakeInvoiceData;
use App\Mail\TenantInvoice\MailReminder;
use App\Mapper\InvoiceDataMapper;
use App\Models\Scopes\TenantInvoiceScope;
use App\Models\TenantInvoice;
use App\Services\TenantInoiceService\TenantInvoiceInterface;
use Barryvdh\DomPDF\Facade\Pdf;
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
        Mail::to($data['invoice']->customer->email)->send(new MailReminder($data['provider'],$data['invoice'], $fileData));
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
        switch ($type) {
            case 'user':
                $data = Auth::user();

                $providerData = [
                    'currency_id' => $data->settings->currency_id,
                    'invoice_language' => $data->settings->invoice_language,
                    'invoice_theme_id' => $data->settings->invoice_theme_id,
                    'vat_percent' => $data->settings->vat_percent,
                    'name' => $data->settings->name,
                    'managing_director' => $data->settings->managing_director,
                    'legal_name' => $data->settings->legal_name,
                    'avatar_url' => $data->settings->avatar_url,
                    'website_url' => $data->settings->website_url,
                    'place_of_jurisdiction' => $data->settings->place_of_jurisdiction,
                    'slug' => $data->settings->slug,
                    'address' => $data->settings->address,
                    'postal_code' => $data->settings->postal_code,
                    'tax_id' => $data->settings->tax_id,
                    'vat_id' => $data->settings->vat_id,
                    'iban' => $data->settings->iban,
                    'account_number' => $data->settings->account_number,
                    'bank_code' => $data->settings->bank_code,
                    'bic' => $data->settings->bic,
                    'contact_number' => $data->settings->contact_number,
                    'contact_email' =>  $data->settings->contact_number
                ];
                break;
            case 'company':
                $data = Auth::user()->companies()->with('settings')->first();
                $providerData = [
                    'currency_id' => $data->settings->currency_id,
                    'invoice_language' => $data->settings->invoice_language,
                    'invoice_theme_id' => $data->settings->invoice_theme_id,
                    'vat_percent' => $data->settings->vat_percent,
                    'name' => $data->name,
                    'managing_director' => $data->managing_director,
                    'legal_name' => $data->legal_name,
                    'avatar_url' => $data->avatar_url,
                    'website_url' => $data->website_url,
                    'place_of_jurisdiction' => $data->place_of_jurisdiction,
                    'slug' => $data->slug,
                    'address' => $data->address,
                    'postal_code' => $data->postal_code,
                    'tax_id' => $data->tax_id,
                    'vat_id' => $data->vat_id,
                    'iban' => $data->iban,
                    'account_number' => $data->account_number,
                    'bank_code' => $data->bank_code,
                    'bic' => $data->bic,
                    'contact_number' => $data->contact_number,
                    'contact_email' =>  $data->contact_number
                ];
                break;
        }
        $invoiceData = new FakeInvoiceData();

        $invoiceData->rgnr = '2024-9999';
        $invoiceData->customer->name = 'Johny English';
        $invoiceData->customer_address = 'Christine-Moritz-Allee 2 52401 Rottenburg';
        $invoiceData->has_vat = true;
        $invoiceData->date_pay = now()->addMonths(5)->firstOfMonth()->toDateString();
        $invoiceData->info = 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore';

        $invoiceItems = [
            ['price' => 99, 'amount' => 1, 'type' => 1, 'description' => 'Neque porro quisquam est qui dolorem ipsum'],
            ['price' => 49, 'amount' => 1, 'type' => 1, 'description' => 'Vquia dolor sit amet, consectetur, adipisci veli'],
            ['price' => 80, 'amount' => 3, 'type' => 2, 'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit'],
        ];

        $invoiceData->invoice_item = array_map(function ($item) {
            return (object) $item;
        }, $invoiceItems);

        $data = [
            'provider' => $providerData,
            'invoice' => $invoiceData
        ];

        $pdf = Pdf::loadView('invoice.new-view', ['data' => $data, 'print' => 1]);

        return $pdf->stream($this->generateFileName($data['invoice']));
    }
}
