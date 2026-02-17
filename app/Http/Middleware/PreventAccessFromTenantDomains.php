<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Stancl\Tenancy\Database\Models\Domain;
use Symfony\Component\HttpFoundation\Response;

class PreventAccessFromTenantDomains
{
    public function handle(Request $request, Closure $next): Response
    {
        $host = $request->getHost();

        if (Domain::where('domain', $host)->exists()) {
            abort(404);
        }

        return $next($request);
    }
}
