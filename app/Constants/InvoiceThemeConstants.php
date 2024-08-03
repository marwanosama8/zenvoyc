<?php

namespace App\Constants;

use App\Models\InvoiceTheme;

class InvoiceThemeConstants
{
    public const DEFAULTID = 1;

    public static function getFormattedThemes()
    {
        $themes = InvoiceTheme::all();
        $options = [];
        foreach ($themes as  $theme) {
            $options[$theme->id] = '
            <div class="flex items-center justify-between">
                <span class="mr-4">' . $theme->name . '</span>
                <div class="flex gap-1">
                    <div class="w-5 h-5 border-2 border-white rounded" style="background-color: ' . $theme->colors[0] . ';"></div>
                    <div class="w-5 h-5 border-2 border-white rounded" style="background-color: ' . $theme->colors[1] . ';"></div>
                    <div class="w-5 h-5 border-2 border-white rounded" style="background-color: ' . $theme->colors[2] . ';"></div>
                </div>
            </div>';
        }
        return $options;
    }
}
