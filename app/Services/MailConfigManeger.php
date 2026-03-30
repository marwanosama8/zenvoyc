<?php

namespace App\Services;

use Illuminate\Support\Facades\Config;

class MailConfigManeger
{
    public  static function setConfigrations(array $providerConfigs)
    {
        foreach ($providerConfigs as $value) {
            Config::set($value['key'], $value['value']);
        }
    }
}
