<?php

namespace App\Jobs;

use App\Mail\Invoice\MailReminder;
use App\Mail\Visualbuilder\EmailTemplates\Invoice;
use App\Services\PanelConfigManager;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class SendTenantInvoiceEmailReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $invoice;

    /**
     * Create a new job instance.
     */
    public function __construct( $invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->invoice->customer->email)->send(new Invoice($this->invoice));
    }
}
