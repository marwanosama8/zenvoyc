<?php

namespace App\Helpers;

use App\Models\Currency;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;

class Helpers
{
    public static function customeHtmlElement($tag, $attributes, $value)
    {
        return new HtmlString('<' . $tag . ' ' . $attributes . ' ' . '>' . $value . '</' . $tag . '>');
    }
    public static function getCurrancyData($currency_id)
    {
        return Currency::find($currency_id);
    }

    public static function getPluckCountries()
    {
        return DB::table('countries')->orderBy('name_en')->pluck('name_en', 'id')->toArray();
    }
}
