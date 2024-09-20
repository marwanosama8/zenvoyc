<?php

namespace App\Mapper;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class OfferDataMapper
{
    public function getData($offer)
    {
        if ($this->getOfferType($offer) == 'App\Models\Company') {
            // get company data
            $data = Company::with('settings')->where('id', $offer->offerable_id)->first();
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
                'city' => $data->city,
                'country' => $data->country,
                'iban' => $data->iban,
                'account_number' => $data->account_number,
                'bank_code' => $data->bank_code,
                'bic' => $data->bic,
                'contact_number' => $data->contact_number,
                'contact_email' =>  $data->contact_email
            ];
        } else { // this in user offer            
            // get user data
            $data = User::with('settings')->where('id', $offer->offerable_id)->first();
            $providerData = [
                'currency_id' => $data->settings->currency_id,
                'invoice_language' => $data->settings->invoice_language,
                'invoice_theme_id' => $data->settings->invoice_theme_id,
                'vat_percent' => $data->settings->vat_percent,
                'name' => $data->settings->name,
                'managing_director' => $data->settings->managing_director,
                'country' => $data->settings->country,
                'legal_name' => $data->settings->legal_name,
                'avatar_url' => $data->settings->avatar_url,
                'website_url' => $data->settings->website_url,
                'place_of_jurisdiction' => $data->settings->place_of_jurisdiction,
                'slug' => $data->settings->slug,
                'address' => $data->settings->address,
                'postal_code' => $data->settings->postal_code,
                'tax_id' => $data->settings->tax_id,
                'city' => $data->city,
                'vat_id' => $data->settings->vat_id,
                'iban' => $data->settings->iban,
                'account_number' => $data->settings->account_number,
                'bank_code' => $data->settings->bank_code,
                'bic' => $data->settings->bic,
                'contact_number' => $data->settings->contact_number,
                'contact_email' =>  $data->settings->contact_number
            ];
        }

        return $providerData;
    }

    public function getOfferType($offer)
    {
        return $offer->offerable_type;
    }
}
