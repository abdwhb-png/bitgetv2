<?php

namespace App\Http\Middleware;

use App\Http\Helpers\RequestHelper;
use Closure;
use Inertia\Inertia;
use Illuminate\Http\Request;

class SetInertiaRootView
{
    public function handle(Request $request, Closure $next)
    {
        $isAdmin = RequestHelper::isAdminDomain($request);

        if ($isAdmin) {
            Inertia::setRootView('admin');
        } else {
            Inertia::setRootView('app');
        }

        return $next($request);
    }
}