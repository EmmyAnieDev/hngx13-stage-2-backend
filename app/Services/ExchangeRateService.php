<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ExchangeRateService
{
    public function getRates(): array
    {
        $url = "https://open.er-api.com/v6/latest/USD";

        $response = Http::timeout(15)->get($url);

        if ($response->failed()) {
            abort(503, json_encode([
                'error' => 'External data source unavailable',
                'details' => 'Could not fetch data from open.er-api.com'
            ]));
        }

        return $response->json()['rates'] ?? [];
    }
}
