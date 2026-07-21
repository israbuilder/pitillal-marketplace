<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Business;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        return Product::query()
            ->where('is_active', true)
            ->when($request->business_id, function ($query) use ($request) {
                $query->where('business_id', $request->business_id);
            })
            ->with('business')
            ->latest()
            ->get();
    }

      public function show(Product $product)
    {
        return $product;
    }

    public function store(Request $request, Business $business)
    {
        abort_if($business->user_id !== $request->user()->id, 403);

        $data = $request->validate([
            'name' => ['required', 'string'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'image' => ['nullable', 'string'],
        ]);

        $product = $business->products()->create($data);

        return response()->json($product, 201);
    }
}