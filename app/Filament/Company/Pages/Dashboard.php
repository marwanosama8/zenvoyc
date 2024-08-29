<?php

namespace App\Filament\Company\Pages;

use Filament\Facades\Filament;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Dashboard\Actions\FilterAction;
use Filament\Pages\Dashboard\Concerns\HasFiltersAction;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Contracts\Support\Htmlable;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Widgets\Concerns\InteractsWithPageFilters;

class Dashboard extends BaseDashboard
{
    use HasFiltersAction;
    use InteractsWithPageFilters;


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
        return __('company.dahboard.head_title');
    }

    protected function getHeaderActions(): array
    {
        return [
            FilterAction::make()
                ->badge(count($this->filters ?? []))
                ->form([
                    DatePicker::make('startDate')
                        ->label(__('dashboard.filter.start_date'))
                        ->default(now()->firstOfYear()->toDateString())
                        ->native(false)
                        ->hint(__('dashboard.filter.start_date.hint')),
                    DatePicker::make('endDate')
                        ->label(__('dashboard.filter.end_date'))
                        ->native(false)
                        ->default(now())
                        ->hint(__('dashboard.filter.end_date.hint')),
                ]),
        ];
    }
}
