<?php

namespace App\Http\Middleware;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RememberTenantMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        try {
            // Get the current tenant (could be User or Company)
            $tenant = Filament::getTenant();

            // If a tenant is found, store its class and ID in the session
            if ($tenant) {
                $tenantData = [
                    'class' => get_class($tenant),
                    'id'    => $tenant->getKey(),
                ];

                $request->session()->put('tenant_data', $tenantData);
            } else {
                $user = auth()->user();
                $tenantData = [
                    'class' => get_class($user),
                    'id'    => $user->id,
                ];

                $request->session()->put('tenant_data', $tenantData);
            }

            return $next($request);
        } catch (\Exception $th) {
            return $next($request);
        }
    }
}
