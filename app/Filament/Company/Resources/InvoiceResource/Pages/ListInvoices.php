<?php

namespace App\Filament\Company\Resources\InvoiceResource\Pages;

use App\Filament\Company\Resources\AutoInvoiceResource\Pages\ListAutoInvoices;
use App\Filament\Company\Resources\InvoiceResource;
use App\Helpers\TenancyHelpers;
use App\Mapper\InvoiceDataMapper;
use App\Services\TenantInvoiceService\PdfGenerator;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make(__('auto_invoices'))
                ->action(fn() => redirect(ListAutoInvoices::getUrl())),
        ];
    }

    public function sendReminderEmail($invoice)
    {
        if (TenancyHelpers::isMailConfigrationsReady()) {
            try {
                $pdfGenerator = new PdfGenerator(new InvoiceDataMapper());
                $pdfGenerator->remind($invoice->rgnr);
                Notification::make('mail_remind_send')->title(__('notfi.send.remind.success.title'))->body(__('remind_send_success_to') . " " . $invoice->customer->name)->success()->send();
            } catch (\Exception $e) {
                Notification::make('mail_exception_catch')->title(__('notfi.send.remind.error.title'))->body(__('Error') . ": " . $e->getMessage())->danger()->send();
            }
        } else {
            Notification::make('mail_configrations_not_ready')->title(__('notfi.send.remind.error.title'))->body(__('notfi.send.remind.error.body'))->danger()->send();
        }
    }
}
