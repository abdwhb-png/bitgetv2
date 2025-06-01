<?php

namespace App\Http\Helpers;

class RouteHelper
{
    public static function getPreviousUrl()
    {
        if (url()->previous() !== route('login') && url()->previous() !== '' && url()->previous() !== url()->current()) {
            return url()->previous();
        } else {
            return 'empty'; // used in javascript to disable back button behavior
        }
    }

    public static function is_adminDomain(): bool
    {
        return RequestHelper::isAdminDomain(request());
    }

    public static function getRoutePrefix()
    {
        return RequestHelper::isAdminDomain(request()) ? config('vars.admin_subdomain') : config('vars.app_subdomain');
    }
}
