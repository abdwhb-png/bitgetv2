<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\LogoutResponse;

class CustomLogoutResponse implements LogoutResponse
{
    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        // Force a full page reload
        return $request->wantsJson()
            ? new JsonResponse('', 204)
            : redirect('/login');
    }
}