<?php

namespace App\Mail\TenantInvoice;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $fileData;
    public $provider;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($provider, $invoice, $fileData)
    {
        $this->provider = $provider;
        $this->invoice = $invoice;
        $this->fileData = $fileData;
    }


    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->from($this->provider['contact_email'])
            ->subject(__('inovice.main.subject', ['legalname' => $this->provider['legal_name'], 'rgnr' => $this->invoice->rgnr,$this->provider['invoice_language']]))
            ->view('emails.tenantinvoice.reminder', ['invoice' => $this->invoice])
            ->attachData($this->fileData['file'], $this->fileData['filename'], [
                'mime' => 'application/pdf',
            ]);
    }
}
