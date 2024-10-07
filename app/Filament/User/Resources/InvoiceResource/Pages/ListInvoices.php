<?php

namespace App\Filament\User\Resources\InvoiceResource\Pages;

use App\Filament\User\Pages\Settings;
use App\Filament\User\Resources\InvoiceResource;
use App\Helpers\TenancyHelpers;
use App\Mapper\InvoiceDataMapper;
use App\Services\TenantInvoiceService\PdfGenerator;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Facades\Filament;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Wizard\Step;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderActions(): array
    {
        $isUserReadyToenerateInvoices = auth()->user()->settings->ready_to_generate;
        return [
            Actions\CreateAction::make()->disabled(!$isUserReadyToenerateInvoices),
            Actions\Action::make(__('auto_invoices'))
                ->disabled(!$isUserReadyToenerateInvoices)
                ->action(fn () => redirect(Filament::getTenant()->slug . "/auto-invoices")),
            Actions\Action::make('Invoice Setting')
            ->label(__('invoice_settings'))
                ->url(Settings::getUrl(['activeTap' => 2]))

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
            Notification::make('mail_configrations_not_ready')->title(__('notfi.send.remind.error.configrations_not_ready'))->body(__('notfi.send.remind.error.you need to set mail configrations'))->actions([
                Action::make('go_to_configrations')
                    ->label(__('notfi.send.go_to_settings'))
                    ->url(Settings::getUrl())
                    ->button()
            ])->danger()->send();
        }
    }
}
