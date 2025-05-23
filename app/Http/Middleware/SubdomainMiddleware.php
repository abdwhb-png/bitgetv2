<?php

namespace App\Http\Middleware;

use App\Http\Helpers\RequestHelper;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

class SubdomainMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $subdomain = RequestHelper::getSubdomain($request);

        // Store it in the request for later use
        $request->attributes->set('subdomain', $subdomain);

        $currentSubdomain = Config::get('vars.current_subdomain');

        if ($subdomain != $currentSubdomain) {
            Config::set('vars.current_subdomain', $subdomain);
        }

        return $next($request);
    }
}