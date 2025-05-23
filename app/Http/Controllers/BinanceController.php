<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BinanceService;
use App\Http\Helpers\UtilsHelper;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class BinanceController extends Controller
{
    private $binance;
    private $client;

    protected $quote = 'quoteAsset';


    public function __construct()
    {
        $this->binance = new BinanceService();
        $this->client = $this->binance->client();
    }


    protected function getData($quotes = ['USDT', 'BTC', 'ETH'], $status = "TRADING")
    {
        $data = env('APP_ENV') == 'local' ? Storage::json('exchangeInfo.json') : $this->client->exchangeInfo();

        if (count($quotes)) {
            $result = collect($data['symbols'])->filter(function ($info) use ($quotes, $status) {
                return in_array($info[$this->quote], $quotes) && $info['status'] === $status;
            });
        } else {
            $result = collect($data['symbols']);
        }

        return $result->map(function ($info) {
            return [
                'symbol' => $info['symbol'],
                'baseAsset' => $info['baseAsset'],
                'quoteAsset' => $info['quoteAsset'],
                'status' => $info['status']
            ];
        });
    }


    public function getExchangeInfo(Request $request): JsonResponse
    {
        $result = [];

        $quotes = $request->input('quotes') ? explode(',', $request->input('quotes')) : UtilsHelper::getPMethods('symbols');
        $status = $request->input('status') ?? "TRADING";

        // Créer une clé de cache unique basée sur les paramètres de la requête
        $cacheKey = 'exchange_info_' . md5(implode(',', $quotes) . $status);

        // Tenter de récupérer les résultats du cache
        /*
        Cache::remember prend en paramètres
        - la clé du cache,
        - la durée de mise en cache (ici, 60 minutes),
        - et une fonction anonyme qui est exécutée si la clé n'existe
        */
        try {
            $result = Cache::store('redis')->remember($cacheKey, 60, function () use ($quotes, $status) {
                return $this->getData();
            });
        } catch (\Exception $e) {
            $result = $this->getData();
        }

        return response()->json($result);
    }


    public function getTickerPrice(Request $request): JsonResponse
    {
        $symbol = $request->input('symbol');
        $symbols = $request->input('symbols');

        $params = [
            'symbol' => $symbol,
            'symbols' => $symbols,
        ];

        $result = $this->binance->getTickerPrice($params);

        return response()->json($result);
    }
}
