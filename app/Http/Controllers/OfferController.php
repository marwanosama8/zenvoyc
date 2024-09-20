<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OfferController extends Controller
{
    public function view($token)
    {
        return view('offers.view', ['token' => $token]);
    }
}
