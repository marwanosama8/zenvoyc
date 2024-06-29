<?php

namespace App\Mail\Invoice;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailReminder extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $fileData;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($invoice, $fileData)
    {
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
            ->subject('[Solution-Work] Erinnerung an Rechnung: RG '.$this->invoice->rgnr)
            ->view('emails.invoice.reminder', ['invoice' => $this->invoice])
            ->attachData($this->fileData['file'], $this->fileData['filename'], [
                'mime' => 'application/pdf',
            ]);
    }
}
