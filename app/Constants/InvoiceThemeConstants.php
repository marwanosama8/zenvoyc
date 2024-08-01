<?php

namespace App\Constants;

class InvoiceThemeConstants
{
    public const DEFAULT = 'Pro-Tool';

    private static $themes =  [
        'Pro-Tool' => [
            'name' => 'Pro-Tool',
            'colors' => ['#1e40af', '#fcfcfc', '#000000'],
        ],
        'Pro-Tool 2' => [
            'name' => 'Pro-Tool 2',
            'colors' => ['#1fdd28', '#fcfcfc', '#000000'],
        ],
    ];

    public static function getFormattedThemes()
    {
        $themes = self::$themes;
        $options = [];
        foreach ($themes as $key => $theme) {
            $options[$key] = '
            <div class="flex items-center justify-between">
                <span class="mr-4">' . $theme['name'] . '</span>
                <div class="flex gap-1">
                    <div class="w-5 h-5 border-2 border-white rounded" style="background-color: ' . $theme['colors'][0] . ';"></div>
                    <div class="w-5 h-5 border-2 border-white rounded" style="background-color: ' . $theme['colors'][1] . ';"></div>
                    <div class="w-5 h-5 border-2 border-white rounded" style="background-color: ' . $theme['colors'][2] . ';"></div>
                </div>
            </div>';
        }
        return $options;
    }
}
