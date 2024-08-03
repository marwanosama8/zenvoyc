<?php

namespace App\Helpers;

use App\Models\Company;
use App\Models\TenantInvoice;
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

   public static function parseManualTime($timeString)
   {
      $pattern = '/(\d+h)?\s*(\d+m)?/';
      preg_match($pattern, $timeString, $matches);

      $hours = 0;
      $minutes = 0;

      if (!empty($matches[1])) {
         $hours = (int) rtrim($matches[1], 'h');
      }

      if (!empty($matches[2])) {
         $minutes = (int) rtrim($matches[2], 'm');
      }
      return [
         'hours' => $hours,
         'minutes' => $minutes,
      ];
   }
   public static function getEndTimeAfterParseToTimeString($startTimeString, $manualTimeString)
   {
      $manualTime = self::parseManualTime($manualTimeString);
      $startTime = Carbon::parse($startTimeString);
      $end_time = $startTime->addHours($manualTime['hours'])->addMinutes($manualTime['minutes']);

      return $end_time->toTimeString();
   }

   public static function getTotalBrutto($getTotalNetto, $provider_vat)
   {
      return ($getTotalNetto + self::getTotalVat($getTotalNetto, $provider_vat));
   }

   public static function getTotalVat($getTotalNetto, $provider_vat)
   {
      return ($getTotalNetto / 100) * $provider_vat;
   }

}
