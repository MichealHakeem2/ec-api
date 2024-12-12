<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\ProductImage;
use App\Models\Product;

class ProductImageController extends Controller
{
    public function store(Request $request, $productId)
    {
        // Validate the uploaded file
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // Find the product by ID
        $product = Product::findOrFail($productId);

        // Upload the image to 'product_images' directory in storage
        $imagePath = $request->file('image')->store('product_images', 'public');

        // Save the image path in the database for the product
        $product->images()->create([
            'image_path' => $imagePath
        ]);

        // Return the image URL in the response
        return response()->json([
            'status' => true,
            'message' => 'Product image uploaded successfully.',
            'image_url' => asset('storage/' . $imagePath),
        ], 201);
    }

    public function show($productId)
    {
        $product = Product::findOrFail($productId);
        $productImages = $product->images;

        $images = $productImages->map(function ($image) {
            return asset('storage/' . $image->image_path);
        });

        return response()->json([
            'status' => true,
            'images' => $images,
        ]);
    }
}

