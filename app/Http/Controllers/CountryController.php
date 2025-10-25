<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Services\CountryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class CountryController extends Controller
{
    /**
     * Refresh country data from external API
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(CountryService $service)
    {
        try {
            // Call the service, which returns an array
            $result = $service->refreshCountries();
            return response()->json($result);
        } catch (\InvalidArgumentException $e) {
            // If the service throws validation errors, return 400 JSON
            return response()->json(json_decode($e->getMessage(), true), 400);
        } catch (\Exception $e) {
            // Optional: catch other exceptions like API failures
            return response()->json([
                'error' => 'Internal server error',
                'details' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Display a listing of countries.
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $query = Country::query();

        if ($request->has('region')) $query->where('region', $request->region);
        if ($request->has('currency')) $query->where('currency_code', $request->currency);

        if ($request->has('sort')) {
            $sort = $request->sort;
            if ($sort === 'gdp_desc') $query->orderByDesc('estimated_gdp');
            elseif ($sort === 'gdp_asc') $query->orderBy('estimated_gdp');
        }

        return response()->json($query->get());
    }

    /**
     * Display the specified country.
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($name)
    {
        $country = Country::whereRaw('LOWER(name) = ?', [strtolower($name)])->first();

        if (!$country)
            return response()->json(['error' => 'Country not found'], 404);

        return response()->json($country);
    }

    /**
     * Remove the specified country from storage.
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($name)
    {
        $country = Country::whereRaw('LOWER(name) = ?', [strtolower($name)])->first();

        if (!$country)
            return response()->json(['error' => 'Country not found'], 404);

        $country->delete();

        return response()->noContent();
    }

    /**
     * Serve the cached summary image
     * @return \Illuminate\Http\Response
     */
    public function image()
    {
        $path = storage_path('app/public/cache/summary.png');
        if (!file_exists($path))
            return response()->json(['error' => 'Summary image not found'], 404);

        return response()->file($path);
    }
}
