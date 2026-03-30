<?php

namespace App\Constants;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use stdClass;

class FakeInvoiceData
{
    public $rgnr = '2024-9999';
    public $customer_address = 'Christine-Moritz-Allee 2 52401 Rottenburg';
    public $has_vat = 1;
    public $date_pay;
    public $vat_percent = 14.00;
    public $info = 'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore';

    public $invoice_item;
    public $customer;

    public $invoiceData;
    public $providerData;

    public $userType;

    public function __construct($userType)
    {
        // init
        $this->userType = $userType;
        
        $this->date_pay = Carbon::now()->addMonths(5)->firstOfMonth()->toDateString();
        $this->customer = new stdClass();
        $this->customer->name = 'Johny English';

        $invoiceItems = [
            ['price' => 99, 'amount' => 1, 'type' => 1, 'description' => 'Neque porro quisquam est qui dolorem ipsum'],
            ['price' => 49, 'amount' => 1, 'type' => 1, 'description' => 'Vquia dolor sit amet, consectetur, adipisci veli'],
            ['price' => 80, 'amount' => 3, 'type' => 2, 'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit'],
        ];

        $this->invoice_item = array_map(function ($item) {
            return (object) $item;
        }, $invoiceItems);
    }

    public function getTotalNetto()
    {
        $total = 0;
        foreach ($this->invoice_item as $item) {
            $total += $item->price * $item->amount;
        }
        return $total;
    }

    public function getData()
    {
        return [
            'provider' => $this->getProviderData(),
            'invoice' => $this->gatInvoiceData()
        ];
    }

    public function gatInvoiceData()
    {
        return $this;
    }


    private function getProviderData()
    {
        switch ($this->userType) {
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

        return $providerData;
    }
}
