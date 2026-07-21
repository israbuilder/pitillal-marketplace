<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DriverLocation;
use Illuminate\Http\Request;

class DriverController extends Controller
{
    public function updateLocation(Request $request)
    {
        abort_if($request->user()->role !== 'driver', 403);

        $data = $request->validate([
            'order_id' => ['nullable', 'exists:orders,id'],
            'lat' => ['required', 'numeric'],
            'lng' => ['required', 'numeric'],
        ]);

        $location = DriverLocation::create([
            'driver_id' => $request->user()->id,
            'order_id' => $data['order_id'] ?? null,
            'lat' => $data['lat'],
            'lng' => $data['lng'],
        ]);

        return response()->json($location, 201);
    }

    public function latestLocation($driverId)
    {
        $location = DriverLocation::where('driver_id', $driverId)
            ->latest()
            ->first();

        return response()->json($location);
    }
}