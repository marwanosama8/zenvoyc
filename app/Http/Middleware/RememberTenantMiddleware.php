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
        // dd(1);
        if (is_null(Filament::getTenant())) {
            $request->session()->put('tenant_id', null);
        } else {
            $request->session()->put('tenant_id', Filament::getTenant()->getKey());
        }

        return $next($request);
    }
}
