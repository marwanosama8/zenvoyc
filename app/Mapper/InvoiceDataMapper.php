<?php

namespace App\Mapper;

use App\Constants\OrderStatus;
use App\Models\Invoice;

class OrderStatusMapper
{
    public function mapForDisplay(Invoice $invoice)  
    {
        return match ($status) {
            OrderStatus::SUCCESS->value => __('Success'),
            OrderStatus::NEW->value => __('New'),
            OrderStatus::REFUNDED->value => __('Refunded'),
            default => __('Pending'),
        };
    }

    private function getInfo()
    {
        if (condition) {
            # code...
        }
    }
}
