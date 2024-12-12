<?php

namespace App\Http\Controllers;

use App\Models\Delivery;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class DeliveryController extends Controller
{
    public function index(): JsonResponse
    {
        $deliveries = Delivery::all();
        return response()->json($deliveries);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'address' => 'required|string|max:255',
            'status' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], Response::HTTP_BAD_REQUEST);
        }

        $delivery = Delivery::create($request->all());
        return response()->json([
            'status' => true,
            'data' => $delivery,
            'message' => 'Delivery created successfully.'
        ], Response::HTTP_CREATED);
    }

    public function show($id): JsonResponse
    {
        $delivery = Delivery::find($id);
        if (!$delivery) {
            return response()->json(['status' => false, 'message' => 'Delivery not found.'], 404);
        }
        return response()->json(['status' => true, 'data' => $delivery]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $delivery = Delivery::find($id);
        if (!$delivery) {
            return response()->json(['status' => false, 'message' => 'Delivery not found.'], 404);
        }

        $delivery->update($request->all());
        return response()->json(['status' => true, 'message' => 'Delivery updated successfully.']);
    }

    public function destroy($id): JsonResponse
    {
        $delivery = Delivery::find($id);
        if (!$delivery) {
            return response()->json(['status' => false, 'message' => 'Delivery not found.'], 404);
        }

        $delivery->delete();
        return response()->json(['status' => true, 'message' => 'Delivery deleted successfully.']);
    }
}
