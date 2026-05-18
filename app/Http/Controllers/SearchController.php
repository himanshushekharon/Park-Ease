<?php

namespace App\Http\Controllers;

use App\Models\ParkingLot;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = ParkingLot::query();

        if ($request->filled('pincode')) {
            $query->where('pincode', $request->input('pincode'));
        }

        // For simplicity, we just use a basic distance calculation or bounding box for live location
        // Real-world would use MongoDB GeoSpatial indexes, but for PHP array filtering works on small datasets
        // Alternatively, since we use `mongodb/laravel-mongodb`, we can use geospatial queries if indexes are set.
        // We will just return all for the demo, or filter by city if needed, 
        // but let's implement a simple Haversine filter after fetching, or basic bounds.

        $parkings = $query->get();

        if ($request->filled('lat') && $request->filled('lng')) {
            $lat = (float) $request->input('lat');
            $lng = (float) $request->input('lng');
            $radius = 10; // 10 km radius

            $parkings = $parkings->filter(function ($parking) use ($lat, $lng, $radius) {
                if (!isset($parking->latitude) || !isset($parking->longitude) || $parking->latitude === '' || $parking->longitude === '') {
                    return false;
                }
                return $this->calculateDistance($lat, $lng, (float)$parking->latitude, (float)$parking->longitude) <= $radius;
            })->values();
        }

        return response()->json(['data' => $parkings]);
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }
}
