<?php

namespace App\Http\Controllers;

use App\Constants\FakeInvoiceData;
use App\Helpers\TenancyHelpers;
use App\Mail\Invoice\MailInvoice as InvoiceMailInvoice;
use App\Mail\Invoice\MailReminder as InvoiceMailReminder;
use App\Mail\MailInvoice;
use App\Mail\MailReminder;
use App\Mapper\InvoiceDataMapper;
use App\Models\Company;
use App\Models\Customer;
use App\Models\TenantInvoice as Invoice;
use App\Models\Scopes\TenantInvoiceScope as InvoiceScope;
use App\Models\TenantInvoice;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
// use Knp\Snappy\Pdf;
use Spatie\Browsershot\Browsershot;
use Barryvdh\DomPDF\Facade\Pdf;
use stdClass;
use Illuminate\Support\Facades\Auth;

class TenantInvoiceController extends Controller
{
    public function __construct(
        private InvoiceDataMapper $invoiceDataMapper,
    ) {
    }


    public function view($invoice)
    {
        $invoice = Invoice::withoutGlobalScope(InvoiceScope::class)->with('customer')->whereRgnr($invoice)->first();
        $data = $this->invoiceDataMapper->getData($invoice);
        // dd($data);
        // $data = [
        //     [
        //         'quantity' => 1,
        //         'description' => '1 Year Subscription',
        //         'price' => '129.00'
        //     ]
        // ];
        $pdf = Pdf::loadView('invoice.new-view', ['data' => $data]);

        return $pdf->stream($this->generateFileName($data['invoice']));
        // abort_if(!$this->isInvoiceAccessable($invoice), 401);
        return view('invoice.new-view', ['data' => $data]);
    }

    public function createFakeInvoice($type)
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
                $data = auth()->user()->companies()->with('settings')->first();
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

        $pdf = Pdf::loadView('invoice.new-view', ['data' => $data]);


        return $pdf->stream($this->generateFileName($data['invoice']));
    }
    public function generate($invoice)
    {
        $invoice = Invoice::withoutGlobalScope(InvoiceScope::class)->whereRgnr($invoice)->first();
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
            $invoice = Invoice::withoutGlobalScope(InvoiceScope::class)->whereRgnr($invoice)->first();
        }
        $pdf = $this->storePdfFile($invoice);
        return  response()->download($pdf['path']);
    }

    public function send($invoice)
    {
        $invoice = Invoice::withoutGlobalScope(InvoiceScope::class)->whereRgnr($invoice)->first();
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
