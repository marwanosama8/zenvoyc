<?php

namespace App\Mapper;

use App\Models\Company;
use Illuminate\Support\Facades\Auth;

class OfferDataMapper
{
    public function getData($offer)
    {
        if ($this->getOfferType($offer) == 'App\Models\Company') {
            // get company data
            $providerData = Company::find($offer->offerable_id);
        } else { // this in user offer            
            // get user data
            $providerData = Auth::user()->settings;
        }

        return $providerData;
    }

    public function getOfferType($offer)
    {
        return $offer->offerable_type;
    }
}
