<?php

namespace App\Mapper;

use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class InvoiceDataMapper
{
    public function getData($invoice)
    {
        if ($this->getInvoiceType($invoice) == 'App\Models\Company') {
            // get company data
            $providerData = Company::find($invoice->invoiceable_id);

            // get invoice data
            $invoiceData = $invoice;
        } else { // this in user invoice            
            // get user data
            $providerData = Auth::user()->userSetting;

            // get invoice data
            $invoiceData = $invoice;
        }

        return [
            'provider' => $providerData,
            'invoice' => $invoiceData
        ];
    }

    public function getInvoiceType($invoice)
    {
        return $invoice->invoiceable_type;
    }
}
