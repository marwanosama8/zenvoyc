<?php

namespace App\Mail\TenantInvoice;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class MailInvoice extends Mailable
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
        return $this->from('buchhaltung@solution-work.de')
	                ->subject('[Solution-Work] Rechnung: RG '.$this->invoice->rgnr)
                    ->view('mail.invoice.invoicemail')
                    ->attachData($this->fileData['file'], $this->fileData['filename'], [
		        'mime' => 'application/pdf',
	        ]);
    }
}
