<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class DiscountController extends Controller
{
    public function index(): JsonResponse
    {
        $discounts = Discount::all();
        return response()->json($discounts);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string|max:255|unique:discounts',
            'amount' => 'required|numeric|min:1',
            'expiration_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], Response::HTTP_BAD_REQUEST);
        }

        $discount = Discount::create($request->all());
        return response()->json([
            'status' => true,
            'data' => $discount,
            'message' => 'Discount created successfully.'
        ], Response::HTTP_CREATED);
    }

    public function show($id): JsonResponse
    {
        $discount = Discount::find($id);
        if (!$discount) {
            return response()->json(['status' => false, 'message' => 'Discount not found.'], 404);
        }
        return response()->json(['status' => true, 'data' => $discount]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $discount = Discount::find($id);
        if (!$discount) {
            return response()->json(['status' => false, 'message' => 'Discount not found.'], 404);
        }

        $discount->update($request->all());
        return response()->json(['status' => true, 'message' => 'Discount updated successfully.']);
    }

    public function destroy($id): JsonResponse
    {
        $discount = Discount::find($id);
        if (!$discount) {
            return response()->json(['status' => false, 'message' => 'Discount not found.'], 404);
        }

        $discount->delete();
        return response()->json(['status' => true, 'message' => 'Discount deleted successfully.']);
    }
}
