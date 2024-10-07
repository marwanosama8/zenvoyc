<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\MenuItem;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use Njxqlus\FilamentProgressbar\FilamentProgressbarPlugin;
use Spatie\Color\Rgb;

class DashboardPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {

        return $panel
            ->id('dashboard')
            ->path('dashboard')
            ->colors([
                'primary' => Color::Red,
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label(__('Admin Panel'))
                    ->visible(
                        fn() => auth()->user()->isAdmin()
                    )
                    ->url(fn() => route('filament.admin.pages.dashboard'))
                    ->icon('heroicon-s-cog-8-tooth'),
                    MenuItem::make()
                    ->label(__('Company Panel'))
                    ->visible(
                        fn() => auth()->user()->hasRole(['company', 'super_company'])
                    )
                    ->url(fn() => route('filament.company.pages.dashboard'))
                    ->icon('heroicon-s-cog-8-tooth'),
                    MenuItem::make()
                    ->label(__('User Panel'))
                    ->visible(
                        fn() => auth()->user()->hasRole('user')
                    )
                    ->url(fn() => route('filament.user.pages.dashboard'))
                    ->icon('heroicon-s-cog-8-tooth'),
            ])
            ->discoverResources(in: app_path('Filament/Dashboard/Resources'), for: 'App\\Filament\\Dashboard\\Resources')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->navigationGroups([
                'Finance',
                'Project',
                'Mangment',
                'Subscription',
            ])
            ->viteTheme('resources/css/filament/dashboard/theme.css')
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->renderHook('panels::head.start', function () {
                return view('components.layouts.partials.analytics');
            })
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugins([
                BreezyCore::make()
                    ->myProfile(
                        shouldRegisterUserMenu: true, // Sets the 'account' link in the panel User Menu (default = true)
                        shouldRegisterNavigation: false, // Adds a main navigation item for the My Profile page (default = false)
                        hasAvatars: false, // Enables the avatar upload form component (default = false)
                        slug: 'my-profile' // Sets the slug for the profile page (default = 'my-profile')
                    ),
                // FilamentProgressbarPlugin::make()->color($color)
            ]);
    }
}
