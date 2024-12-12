<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function index(): JsonResponse
    {
        $orders = Order::all();
        return response()->json($orders);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'total_amount' => 'required|numeric|min:1',
            'status' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], Response::HTTP_BAD_REQUEST);
        }

        $order = Order::create($request->all());
        return response()->json([
            'status' => true,
            'data' => $order,
            'message' => 'Order placed successfully.'
        ], Response::HTTP_CREATED);
    }

    public function show($id): JsonResponse
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['status' => false, 'message' => 'Order not found.'], 404);
        }
        return response()->json(['status' => true, 'data' => $order]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['status' => false, 'message' => 'Order not found.'], 404);
        }

        $order->update($request->all());
        return response()->json(['status' => true, 'message' => 'Order updated successfully.']);
    }

    public function destroy($id): JsonResponse
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['status' => false, 'message' => 'Order not found.'], 404);
        }

        $order->delete();
        return response()->json(['status' => true, 'message' => 'Order canceled successfully.']);
    }
}
