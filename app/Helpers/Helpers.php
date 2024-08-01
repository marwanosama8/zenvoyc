<?php

namespace App\Helpers;

use App\Models\Company;
use App\Models\Project;
use App\Models\Task;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\HtmlString;
use Spatie\Permission\Models\Role;

class Helpers
{
    public static function customeHtmlElement($tag, $attributes, $value)
    {
        return new HtmlString('<' . $tag . ' ' . $attributes . ' ' . '>' . $value . '</' . $tag . '>');
    }
}
