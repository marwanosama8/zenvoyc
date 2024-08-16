<?php

namespace App\Services\TenantInvoiceService;

use App\Helpers\CalculationHelpers;
use App\Mapper\InvoiceDataMapper;
use App\Models\Currency;
use App\Models\Scopes\TenantInvoiceScope;
use App\Models\TenantInvoice;
use Carbon\Carbon;
use horstoeko\zugferd\codelists\ZugferdPaymentMeans;
use horstoeko\zugferd\ZugferdDocumentBuilder;
use Illuminate\Support\Facades\Storage;
use Spatie\TemporaryDirectory\TemporaryDirectory;
use Str;

class XmlGenerator
{
    public function __construct(
        private InvoiceDataMapper $invoiceDataMapper
    ) {}

    public function download($rgnr, $profile)
    {
        $document = $this->generate($rgnr, $profile);
        $xmlContent = $document->getContent();
        $uuid = Str::uuid();

        $temporaryDirectory = (new TemporaryDirectory())->create();
        $tempFilePath = $temporaryDirectory->path("{$uuid}.xml");
    
        $data = $this->getData($rgnr);
        $fileName = $this->generateFileName($data['invoice']);

        Storage::disk('local')->put($tempFilePath, $xmlContent);
    
        return response()->download(storage_path('app/' . $tempFilePath),"{$fileName}")->deleteFileAfterSend(true);
    }

    public function temporaryGenerate($rgnr, $profileId)
    {
        $temporaryDirectory = (new TemporaryDirectory())->create();
        $uuid = Str::uuid();

        $xmlPath = $temporaryDirectory->path("{$uuid}.xml");
    
        $document = $this->generate($rgnr, $profileId);
        $xmlContent = $document->getContent();

        Storage::disk('local')->put($xmlPath, $xmlContent);

        return $xmlPath;
    }

    public function generate($rgnr, $profileId)
    {

        $invoice = TenantInvoice::withoutGlobalScope(TenantInvoiceScope::class)->with('customer')->whereRgnr($rgnr)->first();
        $data = $this->invoiceDataMapper->getData($invoice);
        $provider = $data['provider'];
        $invoice = $data['invoice'];

        $document = ZugferdDocumentBuilder::CreateNew((int)$profileId);

        $currency = Currency::find($provider['currency_id']);
        // Set document information
        $document->setDocumentInformation(
            $invoice->rgnr,
            '380',
            \DateTime::createFromFormat("Ymd", date('Ymd')),
            $currency->code,
            null,
            $provider['invoice_language'],
            null
        )
            ->setDocumentSupplyChainEvent(Carbon::createFromFormat('d.m.Y', $invoice->date_end))
            ->addDocumentPaymentMean(
                ZugferdPaymentMeans::UNTDID_4461_58,
                null,
                null,
                null,
                null,
                null,
                $provider['iban'],
                $provider['name'],
                null,
                $provider['bic']
            )

            ->addDocumentPaymentTerm(__('invoice-template.payable_at', ['date' => $invoice->date_pay]), Carbon::createFromFormat('d.m.Y', $invoice->date_pay))
            ->setDocumentSeller($provider['name'])
            ->addDocumentSellerGlobalId($provider['vat_id'])
            ->setDocumentBuyerReference($invoice->customer->reference)


            ->addDocumentBuyerTaxRegistration("VA", $invoice->customer->vat_id)
            ->addDocumentSellerTaxRegistration("VA", $provider['vat_id'])

            ->setDocumentSellerContact($provider['name'], $provider['place_of_jurisdiction'], $provider['contact_number'], $provider['contact_number'], $provider['contact_email'])
            ->setDocumentBuyer($invoice->customer->name, null)
            ->setDocumentSellerAddress(
                $provider['address'],
                '',
                '',
                $provider['postal_code'],
                $provider['city'],
                strtoupper($provider['country']->code)

            )
            ->setDocumentBuyerAddress(
                $invoice->customer->fullCustomerAddress,
                '',
                '',
                $invoice->customer->zip,
                $invoice->customer->city,
                strtoupper($invoice->customer->country->code)
            );

        if (filled($invoice['info'])) {
            $document->addDocumentNote($invoice['info']);
        }

        // add taxes
        $brutto = CalculationHelpers::getTotalBrutto($invoice->getTotalNetto(), $provider['vat_percent']);
        $vat = CalculationHelpers::getTotalVat($invoice->getTotalNetto(), $provider['vat_percent']);

        $document->addDocumentTax('S', 'VAT', $invoice->getTotalNetto(), $vat, $provider['vat_percent']);

        $document->setDocumentSummation(
            $brutto,
            $brutto,
            $invoice->getTotalNetto(),
            0.00,
            0.00,
            $invoice->getTotalNetto(),
            $vat,
            null,
            null
        );


        foreach ($invoice->invoice_item as $index => $item) {
            $unitPrice = $item['price'] / $item['amount'];

            $document
                ->addNewPosition((string)($index + 1))
                ->setDocumentPositionProductDetails(
                    $item['description'],
                    '',
                    null,
                    null,
                    '',
                    null
                )
                ->setDocumentPositionGrossPrice($unitPrice)
                ->setDocumentPositionNetPrice($unitPrice)
                ->setDocumentPositionQuantity($item['amount'], 'H87')
                ->addDocumentPositionTax('S', 'VAT', $provider['vat_percent'])
                ->setDocumentPositionLineSummation($item['price']);
        }

        return $document;
    }

    public function getData($rgnr)
    {
        $invoice = TenantInvoice::withoutGlobalScope(TenantInvoiceScope::class)->with('customer')->whereRgnr($rgnr)->first();
        return  $this->invoiceDataMapper->getData($invoice);
    }

    public function generateFileName($invoice): string
    {
        return "RG {$invoice->rgnr} {$invoice->customer->name}.xml";
    }

}
