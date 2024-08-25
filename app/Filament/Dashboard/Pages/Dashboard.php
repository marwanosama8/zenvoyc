<?php

namespace App\Filament\Dashboard\Pages;


use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Filament\Support\Facades\FilamentIcon;

use Illuminate\Contracts\Support\Htmlable;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    use HasFiltersAction;

    protected static string $routePath = '/';

    protected static ?int $navigationSort = -2;

    /**
     * @var view-string
     */
    protected static string $view = 'filament-panels::pages.dashboard';

    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel ??
            static::$title ??
            __('filament-panels::pages/dashboard.title');
    }

    public function __construct() {}


    public static function getNavigationIcon(): string | Htmlable | null
    {
        return static::$navigationIcon
            ?? FilamentIcon::resolve('panels::pages.dashboard.navigation-item')
            ?? (Filament::hasTopNavigation() ? 'heroicon-m-home' : 'heroicon-o-home');
    }

    public static function getRoutePath(): string
    {
        return static::$routePath;
    }

    /**
     * @return int | string | array<string, int | string | null>
     */
    public function getColumns(): int | string | array
    {
        return 2;
    }

    public function getTitle(): string | Htmlable
    {
        return __('dashboard.dahboard.head_title');
    }

    protected function getHeaderActions(): array
    {
        return [
            FilterAction::make()
                ->form([
                    DatePicker::make('startDate')
                        ->label(__('dashboard.filter.start_date'))
                        ->default(now()->firstOfYear()->toDateString())
                        ->native(false)
                        // ->format('d/m/Y')
                        ->hint(__('dashboard.filter.start_date.hint')),
                    DatePicker::make('endDate')
                        ->label(__('dashboard.filter.end_date'))
                        // ->format('d/m/Y')
                        ->native(false)
                        ->default(now())
                        ->hint(__('dashboard.filter.end_date.hint')),
                ]),
        ];
    }
}
