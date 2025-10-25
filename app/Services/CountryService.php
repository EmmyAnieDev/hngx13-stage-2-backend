<?php

namespace App\Services;

use App\Models\Country;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CountryService
{
    protected $exchangeService;

    public function __construct(ExchangeRateService $exchangeService)
    {
        $this->exchangeService = $exchangeService;
    }

    public function refreshCountries(): array
    {
        $countryUrl = "https://restcountries.com/v2/all?fields=name,capital,region,population,flag,currencies";

        $response = Http::timeout(15)->get($countryUrl);

        if ($response->failed()) {
            abort(503, json_encode([
                'error' => 'External data source unavailable',
                'details' => 'Could not fetch data from restcountries.com'
            ]));
        }

        $countries = $response->json();
        $rates = $this->exchangeService->getRates();
        $now = now();

        // Prepare all data for bulk upsert
        $countryData = [];

        foreach ($countries as $data) {
            $currencyCode = $data['currencies'][0]['code'] ?? null;
            $exchangeRate = $currencyCode ? ($rates[$currencyCode] ?? null) : null;

            $estimatedGdp = null;
            if (!$currencyCode) {
                $estimatedGdp = 0;
            } elseif ($exchangeRate) {
                $randomFactor = rand(1000, 2000);
                $estimatedGdp = ($data['population'] * $randomFactor) / $exchangeRate;
            }

            $countryData[] = [
                'name' => $data['name'],
                'capital' => $data['capital'] ?? null,
                'region' => $data['region'] ?? null,
                'population' => $data['population'] ?? 0,
                'currency_code' => $currencyCode,
                'exchange_rate' => $exchangeRate,
                'estimated_gdp' => $estimatedGdp,
                'flag_url' => $data['flag'] ?? null,
                'last_refreshed_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Single bulk upsert operation (SUPER FAST!)
        // This does insert + update in ONE query
        Country::upsert(
            $countryData,
            ['name'], // Match on name column
            ['capital', 'region', 'population', 'currency_code', 'exchange_rate', 
             'estimated_gdp', 'flag_url', 'last_refreshed_at', 'updated_at']
        );

        $total = count($countryData);

        $last_refreshed_at = now()->utc()->format('Y-m-d\TH:i:s\Z');

        Cache::put('last_refreshed_at', $last_refreshed_at, now()->addDay());
        Cache::put('total_countries', $total, now()->addDay());

        // Generate summary image
        app('App\Helpers\ImageHelper')->generateSummaryImage();

        return [
            'total_countries' => $total,
            'last_refreshed_at' => $last_refreshed_at
        ];

    }
}