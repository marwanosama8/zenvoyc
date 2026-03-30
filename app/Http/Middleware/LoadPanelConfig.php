<?php

namespace App\Http\Middleware;

use App\Helpers\TenancyHelpers;
use App\Models\Company;
use App\Models\User;
use App\Services\PanelConfigManager;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class LoadPanelConfig
{
    protected $configManager;

    public function __construct(PanelConfigManager $configManager)
    {
        $this->configManager = $configManager;
    }

    public function handle($request, Closure $next)
    {
        $panelConfgiManger = new PanelConfigManager();


        $smtpSettings = [
            'mail.mailers.smtp.host' => $panelConfgiManger->get('mail.mailers.smtp.host'),
            'mail.mailers.smtp.port' => $panelConfgiManger->get('mail.mailers.smtp.port'),
            'mail.mailers.smtp.username' => $panelConfgiManger->get('mail.mailers.smtp.port'),
            'mail.mailers.smtp.password' => $panelConfgiManger->get('mail.mailers.smtp.port'),
        ];

        foreach ($smtpSettings as $key => $value) {
            $this->configManager->set($key, $value);
        }

        return $next($request);
    }
}
