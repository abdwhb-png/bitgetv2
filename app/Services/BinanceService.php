<?php

namespace App\Services;

use App\Enums\WalletsEnum;
use BinanceApi\Binance;
use Illuminate\Support\Facades\Log;


class BinanceService
{
    protected $binance;

    public function __construct()
    {
        $gClient = new \GuzzleHttp\Client([
            'timeout' => 120, // Set timeout to 120 seconds
        ]);

        $apiKey = env('BINANCE_API_KEY');
        $apiSecret = env('BINANCE_API_SECRET');

        $endPoint = env('APP_ENV') == 'local' ? 'https://api.binance.com' : 'https://api.binance.us';

        $this->binance = new Binance(endpoint: $endPoint, client: $gClient);

        if ($apiKey && $apiSecret) {
            $this->binance->setApiKeys($apiKey, $apiSecret);
        }

        if ($this->binance) {
            $this->binance->setOutputCallback(function ($output) {
                return $output['response']['data'];
            });
        } else {
            throw new \Exception('Binance Service client instance not created.');
        }
    }


    protected function getParam(string $key, array $params)
    {
        if (array_key_exists($key, $params) && !empty($params[$key])) {
            return $params[$key];
        }

        return null;
    }


    protected function goodSymbol(string $symbol): string
    {
        $gSymbols = config('vars.good_symbols');

        if (in_array($symbol, array_keys($gSymbols))) {
            return $gSymbols[$symbol];
        }

        return $symbol;
    }


    protected function parseSymbol($symbol): string
    {
        $symbol = str_replace(WalletsEnum::USDTTRC20->symbol(), 'USDT', $symbol);
        $symbol = str_replace(WalletsEnum::USDTERC20->symbol(), 'USDT', $symbol);

        return $this->goodSymbol($symbol);
    }


    public function client(): Binance
    {
        return $this->binance;
    }


    public function getTime()
    {
        return \BinanceApi\Docs\GeneralInfo\Signed::binanceMicrotime();
    }


    public function getExchangeInfo(array $quotes) {}


    public function getTickerPrice(array $params = [])
    {
        try {
            if ($this->getParam('symbol', $params)) {
                $symbol = $this->parseSymbol($params['symbol']);

                return $this->binance->tickerPrice(symbol: $symbol);
            }

            if ($this->getParam('symbols', $params)) {
                $symbols = collect($params['symbols'])->map(function ($symbol) {
                    return $this->parseSymbol($symbol);
                });

                return $this->binance->tickerPrice(symbols: $symbols->toArray());
            }
        } catch (\Throwable $th) {
            Log::channel('binance')->error('Error while getting ticker price : ' . $th->getMessage());
        }
    }
}
