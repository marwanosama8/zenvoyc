<?php
namespace App\Jobs;

use App\Helpers\TenancyHelpers;
use App\Mail\Visualbuilder\EmailTemplates\InvoiceReminderEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use App\Models\User;
use App\Services\MailConfigManeger;

class SendReminderEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $invoice;
    protected $data;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param array $emailData
     */
    public function __construct($invoice)
    {
        $this->invoice = $invoice['invoice'];
        $this->data = $invoice;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $smtpSettings = TenancyHelpers::getTenantModelOutSideFilament()->configs()->get(['key','value'])->toArray();
        MailConfigManeger::setConfigrations($smtpSettings);
        Mail::to($this->invoice->customer->email)->send(new InvoiceReminderEmail($this->invoice,$this->data));
    }
}
