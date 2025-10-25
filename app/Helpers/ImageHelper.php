<?php

namespace App\Helpers;

use App\Models\Country;
use Illuminate\Support\Facades\Cache;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageHelper
{
    public function generateSummaryImage(): void
    {
        $manager = new ImageManager(new Driver());

        $total = Cache::get('total_countries', Country::count());
        $top = Country::orderByDesc('estimated_gdp')->take(5)->get(['name', 'estimated_gdp']);
        $lastRefreshed = Cache::get('last_refreshed_at', now());

        $text = "Summary Image\n"
            . "Total Countries: $total\n"
            . "Last Refreshed: $lastRefreshed\n\n"
            . "Top 5 by GDP:\n";

        foreach ($top as $c) {
            $text .= "{$c->name} - " . number_format($c->estimated_gdp, 2) . "\n";
        }

        $image = $manager->create(600, 400)->fill('#f4f4f4');
        $image->text($text, 30, 50, function ($font) {
            $font->size(16);
            $font->color('#111');
        });

        $path = storage_path('app/public/cache/summary.png');
        @mkdir(dirname($path), 0755, true);
        $image->save($path);
    }
}
