<?php

namespace App\Constants;

use stdClass;

class FakeInvoiceData
{
    public $rgnr;
    public $customer;
    public $customer_address;
    public $has_vat;
    public $date_pay;
    public $invoice_item;
    public $info;

    public function __construct() {
        $this->customer = new stdClass();
    }

    public function getTotalNetto() {
        $total = 0;
        foreach ($this->invoice_item as $item) {
            $total += $item->price * $item->amount;
        }
        return $total;
    }
}

