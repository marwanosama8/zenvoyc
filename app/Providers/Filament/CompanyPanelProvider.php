<?php

namespace App\Providers\Filament;

use App\Filament\Company\Dashboard;
use App\Filament\Company\Pages\Tenancy\RegisterCompany;
use App\Filament\Company\Pages\Tenancy\EditCompanyProfile;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use App\Filament\Company\Pages;
use App\Helpers\TenancyHelpers;
use App\Http\Middleware\RememberTenantMiddleware;
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
use Filament\Navigation\MenuItem;
use Jeffgreco13\FilamentBreezy\BreezyCore;
use App\Livewire\Filament\MyProfilePersonalInfo;
use App\Models\Company;
use Filament\Facades\Filament;
use Njxqlus\FilamentProgressbar\FilamentProgressbarPlugin;
use Spatie\Color\Rgb;
use Visualbuilder\EmailTemplates\EmailTemplatesPlugin;

class CompanyPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        $myArr = explode(', ', Color::Orange[500]);
        $hex = new Rgb(...$myArr);
        $color = $hex->toHex()->__toString();
        
        return $panel
            ->id('company')
            ->path('company')
            ->colors([
                'primary' => Color::Orange,
            ])
            ->userMenuItems([
                MenuItem::make()
                    ->label(__('Admin Panel'))
                    ->visible(
                        fn () => auth()->user()->isAdmin()
                    )
                    ->url(fn () => route('filament.admin.pages.dashboard'))
                    ->icon('heroicon-s-cog-8-tooth'),
            ])
            ->tenantMenuItems([
                'profile' => MenuItem::make()->hidden(),
                MenuItem::make()
                ->label('Settings')
                ->url(fn (): string => Filament::getUrl() .'/company-settings')
                ->icon('heroicon-m-cog-8-tooth'),
            ])
            ->discoverResources(in: app_path('Filament/Company/Resources'), for: 'App\\Filament\\Company\\Resources')
            ->discoverPages(in: app_path('Filament/Company/Pages'), for: 'App\\Filament\\Company\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->navigationGroups([
                'Finance',
                'Project',
                'Mangment',
            ])
            ->viteTheme('resources/css/filament/company/theme.css')

            ->discoverWidgets(in: app_path('Filament/Company/Widgets'), for: 'App\\Filament\\Company\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
            ])
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
            ->tenantMiddleware([
                \App\Http\Middleware\RememberTenantMiddleware::class,
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
                    )
                    ->myProfileComponents([
                        'personal_info' => MyProfilePersonalInfo::class,
                    ]),
                FilamentProgressbarPlugin::make()->color($color),
                EmailTemplatesPlugin::make(),

            ])
            ->tenantMiddleware([
                RememberTenantMiddleware::class,
            ])
            ->tenant(Company::class, slugAttribute: 'slug', ownershipRelationship: 'company')
            ->tenantProfile(EditCompanyProfile::class)
            ->tenantRegistration(RegisterCompany::class);
    }
}
