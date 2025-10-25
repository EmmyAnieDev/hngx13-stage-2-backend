<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;

class StatusController extends Controller
{
    /**
     * Display application status.
     * @return \Illuminate\Http\JsonResponse
    */
    public function show()
    {
        return response()->json([
            'total_countries' => Cache::get('total_countries', 0),
            'last_refreshed_at' => Cache::get('last_refreshed_at', null)
        ]);
    }
}
