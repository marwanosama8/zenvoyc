<?php

namespace App\Helpers;

use App\Models\Company;
use Carbon\Carbon;
use Filament\Facades\Filament;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;

class CalculationHelpers
{
    public static function calculateNextGenerateDate($value)
    {
        $now = Carbon::now();

        return match ($value) {
            1 => $now->addMonth()->format('Y-m-d'),
            2 => $now->addMonths(3)->format('Y-m-d'),
            3 => $now->addYear()->format('Y-m-d'),
        };
    }
}
