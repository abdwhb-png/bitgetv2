<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProxyController extends Controller
{
    public function proxyToBinance()
    {
        // $url = 'https://api.binance.com/api/v3/exchangeInfo';
        $url = request()->input('url');
        $client = new \GuzzleHttp\Client();
        $response = $client->request('GET', $url);

        return response($response->getBody(), $response->getStatusCode())
            ->header('Content-Type', $response->getHeaderLine('Content-Type'));
    }
}
