<?php

namespace App\Http\Middleware;

use Inertia\Middleware;
use Illuminate\Http\Request;
use App\Models\SystemSetting;
use App\Models\CustomerService;
use App\Http\Helpers\RouteHelper;
use App\Http\Helpers\RequestHelper;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\SystemSettingResource;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            // Synchronously...
            'routePrefix' => RequestHelper::getSubdomain($request) . '.',
            'isAdminDomain' => RequestHelper::isAdminDomain($request),
            'urlPrev' => RouteHelper::getPreviousUrl(),
            'tv' => config('vars.trading_view'),
            'symbols_swiper' => config('vars.symbols_swiper'),
            "customerServices" => CustomerService::where('status', 1)->get(),
            "siteConfig" => new SystemSettingResource(SystemSetting::first() ?? new SystemSetting()),

            // Lazily...
            'auth.user' => fn() => $request->user()
                ? new UserResource($request->user())
                : null,
            'flash' => [
                'status' => fn() => $request->session()->get('status'),
            ],
        ]);
    }
}