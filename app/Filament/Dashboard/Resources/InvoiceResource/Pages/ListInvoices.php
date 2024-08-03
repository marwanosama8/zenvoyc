<?php

namespace App\Filament\Dashboard\Resources\InvoiceResource\Pages;

use App\Filament\Dashboard\Pages\Settings;
use App\Filament\Dashboard\Resources\InvoiceResource;
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
                ->url(Settings::getUrl(['activeTap' => 2]))

        ];
    }
}
