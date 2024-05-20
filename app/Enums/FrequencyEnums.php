<?php

namespace App\Enums;

enum FrequencyEnums: string
{
    case OneTime = 'one-time';
    case Monthly = 'monthly';
    case Quarterly = 'quarterly';
    case Yearly = 'yearly';
}
