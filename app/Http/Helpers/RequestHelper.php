<?php

namespace App\Http\Helpers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;

class RequestHelper
{
    /**
     * Get the subdomain from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string
     */
    public static function getSubdomain(Request $request): string
    {
        $host = $request->getHost();
        $parts = explode('.', $host);

        // Return the first part of the domain if available, otherwise return an empty string
        return $parts[0] ?? '';
    }

    /**
     * Check if the current subdomain matches the admin subdomain.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public static function isAdminDomain(Request $request): bool
    {
        return Str::startsWith(config('vars.admin_subdomain'), self::getSubdomain($request));
    }
}