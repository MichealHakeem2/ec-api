<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function index(): JsonResponse
    {
        $payments = Payment::all();
        return response()->json($payments);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], Response::HTTP_BAD_REQUEST);
        }

        $payment = Payment::create($request->all());
        return response()->json([
            'status' => true,
            'data' => $payment,
            'message' => 'Payment processed successfully.'
        ], Response::HTTP_CREATED);
    }

    public function show($id): JsonResponse
    {
        $payment = Payment::find($id);
        if (!$payment) {
            return response()->json(['status' => false, 'message' => 'Payment not found.'], 404);
        }
        return response()->json(['status' => true, 'data' => $payment]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $payment = Payment::find($id);
        if (!$payment) {
            return response()->json(['status' => false, 'message' => 'Payment not found.'], 404);
        }

        $payment->update($request->all());
        return response()->json(['status' => true, 'message' => 'Payment updated successfully.']);
    }

    public function destroy($id): JsonResponse
    {
        $payment = Payment::find($id);
        if (!$payment) {
            return response()->json(['status' => false, 'message' => 'Payment not found.'], 404);
        }

        $payment->delete();
        return response()->json(['status' => true, 'message' => 'Payment deleted successfully.']);
    }
}
