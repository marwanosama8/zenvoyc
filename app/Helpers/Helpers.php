<?php

namespace App\Helpers;

use App\Models\Currency;
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
}
