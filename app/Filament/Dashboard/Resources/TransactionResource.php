<?php

namespace App\Filament\Dashboard\Resources;

use App\Constants\TransactionStatus;
use App\Filament\Dashboard\Resources\OrderResource\Pages\ViewOrder;
use App\Filament\Dashboard\Resources\SubscriptionResource\Pages\ViewSubscription;
use App\Filament\Dashboard\Resources\TransactionResource\Pages;
use App\Mapper\TransactionStatusMapper;
use App\Models\Transaction;
use App\Services\ConfigManager;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class TransactionResource extends Resource
{
    protected static ?string $model = Transaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-currency-dollar';
    public static function getModelLabel(): string
    {
        return __('navigation.payment');
    }

  
    public static function getPluralModelLabel(): string
    {
        return __('navigation.payments');
    }
    public static function getNavigationLabel(): string
    {
        return __('navigation.payment');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('navigation.subscription');
    }
    public static function form(Form $form): Form
    {
        return $form
            ->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('amount')->formatStateUsing(function (string $state, $record) {
                    return money($state, $record->currency->code);
                }),
                Tables\Columns\TextColumn::make('status')
                    ->formatStateUsing(fn (string $state, TransactionStatusMapper $mapper): string => $mapper->mapForDisplay($state)),
                Tables\Columns\TextColumn::make('owner')
                    ->label(__('Owner'))
                    ->getStateUsing(fn (Transaction $record) => $record->subscription_id !== null ? ($record->subscription->plan?->name ?? '-') : ($record->order_id !== null ? __('View Order') : '-'))
                    ->url(fn (Transaction $record) => $record->subscription_id !== null  ? ViewSubscription::getUrl(['record' => $record->subscription ]) : ($record->order_id !== null ? ViewOrder::getUrl(['record' => $record->order]) : '-')),
                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('Date'))
                    ->dateTime(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListTransactions::route('/'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canDeleteAny(): bool
    {
        return false;
    }

    public static function canUpdate(Model $record): bool
    {
        return false;
    }

    public static function canUpdateAny(): bool
    {
        return false;
    }

    public static function canViewAny(): bool
    {
        return true;  // we want to ignore the default permission check (from the policy) and allow all users to view their own transactions
    }

    public static function canEdit(Model $record): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('user_id', auth()->user()->id)->where('amount' , '>', 0)->where('status', '!=', TransactionStatus::NOT_STARTED->value);
    }


    public static function isDiscovered(): bool
    {
        return app()->make(ConfigManager::class)->get('app.customer_dashboard.show_transactions', true);
    }
}
