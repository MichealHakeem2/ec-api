<?php

namespace App\Http\Controllers;

use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class ProductReviewController extends Controller
{
    public function index(): JsonResponse
    {
        $reviews = ProductReview::all();
        return response()->json($reviews);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'user_id' => 'required|exists:users,id',
            'rating' => 'required|numeric|between:1,5',
            'comment' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], Response::HTTP_BAD_REQUEST);
        }

        $review = ProductReview::create($request->all());
        return response()->json([
            'status' => true,
            'data' => $review,
            'message' => 'Review added successfully.'
        ], Response::HTTP_CREATED);
    }

    public function show($id): JsonResponse
    {
        $review = ProductReview::find($id);
        if (!$review) {
            return response()->json(['status' => false, 'message' => 'Review not found.'], 404);
        }
        return response()->json(['status' => true, 'data' => $review]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $review = ProductReview::find($id);
        if (!$review) {
            return response()->json(['status' => false, 'message' => 'Review not found.'], 404);
        }

        $review->update($request->all());
        return response()->json(['status' => true, 'message' => 'Review updated successfully.']);
    }

    public function destroy($id): JsonResponse
    {
        $review = ProductReview::find($id);
        if (!$review) {
            return response()->json(['status' => false, 'message' => 'Review not found.'], 404);
        }

        $review->delete();
        return response()->json(['status' => true, 'message' => 'Review deleted successfully.']);
    }
}
