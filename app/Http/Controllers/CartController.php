<?php

namespace App\Http\Controllers;

use App\Models\Cart; // Example import
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse; // Add this line

class CartController extends Controller
{
    public function show($id): JsonResponse
    {
        // Find the cart by ID
        $cart = Cart::find($id);

        // If the cart is not found, return a 404 response
        if (!$cart) {
            return response()->json([
                'status' => false,
                'message' => 'Cart not found.'
            ], 404);
        }

        // Return the cart data
        return response()->json([
            'status' => true,
            'data' => $cart
        ]);
    }
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], Response::HTTP_BAD_REQUEST);
        }

        $cart = Cart::create($request->all());

        return response()->json([
            'status' => true,
            'data' => $cart,
            'message' => 'Item added to cart.'
        ], Response::HTTP_CREATED);
    }

    public function destroy($id): JsonResponse
    {
        $cart = Cart::find($id);
        if (!$cart) {
            return response()->json(['status' => false, 'message' => 'Cart item not found.'], 404);
        }

        $cart->delete();

        return response()->json(['status' => true, 'message' => 'Cart item removed.'], 200);
    }
}
