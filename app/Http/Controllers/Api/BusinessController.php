<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    public function index()
    {
        return Business::query()
            ->where('is_active', true)
            ->withCount('products')
            ->latest()
            ->get();
    }

    public function show(Business $business)
    {
        return $business->load('products');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'phone' => ['nullable', 'string'],
            'address' => ['nullable', 'string'],
            'lat' => ['nullable', 'numeric'],
            'lng' => ['nullable', 'numeric'],
        ]);

        $business = Business::create([
            ...$data,
            'user_id' => $request->user()->id,
            'is_active' => false,
        ]);

        return response()->json($business, 201);
    }
}