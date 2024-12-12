<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse; // Add this line


class CartItemController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'cart_id' => 'required|exists:cart,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|numeric|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], Response::HTTP_BAD_REQUEST);
        }

        $cartItem = CartItem::create($request->all());

        return response()->json([
            'status' => true,
            'data' => $cartItem,
            'message' => 'Cart item added successfully.'
        ], Response::HTTP_CREATED);
    }

    public function destroy($id): JsonResponse
    {
        $cartItem = CartItem::find($id);
        if (!$cartItem) {
            return response()->json(['status' => false, 'message' => 'Cart item not found.'], 404);
        }

        $cartItem->delete();

        return response()->json(['status' => true, 'message' => 'Cart item removed.'], 200);
    }
}
