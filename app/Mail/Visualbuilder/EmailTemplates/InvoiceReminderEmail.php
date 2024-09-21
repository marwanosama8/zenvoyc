<?php

namespace App\Mail\Visualbuilder\EmailTemplates;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Visualbuilder\EmailTemplates\Traits\BuildGenericEmail;
use Barryvdh\DomPDF\Facade\Pdf;

class InvoiceReminderEmail extends Mailable
{
    use Queueable;
    use SerializesModels;
    use BuildGenericEmail;

    public $template = 'invoice-reminder-email';
    public $invoice;
    public $attachment;
    public $sendTo;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($invoice,$alldata)
    {
        $this->invoice = $invoice;
        $this->attachment = [
            'file' => Pdf::loadView('invoice.new-view', ['data' => $alldata, 'print' => 1])->output(),
            'filename' => $this->generateFileName($invoice)
        ];
        $this->sendTo = $invoice->customer->email;
    }
    public function generateFileName($invoice): string
    {
        return "RG {$invoice->rgnr} {$invoice->customer->name}.pdf";
    }

}
