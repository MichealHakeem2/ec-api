<?php

namespace App\Http\Controllers;

use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class UserAddressController extends Controller
{
    public function index(): JsonResponse
    {
        $addresses = UserAddress::all();
        return response()->json($addresses);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'address_line_1' => 'required|string|max:255',
            'address_line_2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'postal_code' => 'required|string|max:10',
            'country' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], Response::HTTP_BAD_REQUEST);
        }

        $address = UserAddress::create($request->all());
        return response()->json([
            'status' => true,
            'data' => $address,
            'message' => 'Address added successfully.'
        ], Response::HTTP_CREATED);
    }

    public function show($id): JsonResponse
    {
        $address = UserAddress::find($id);
        if (!$address) {
            return response()->json(['status' => false, 'message' => 'Address not found.'], 404);
        }
        return response()->json(['status' => true, 'data' => $address]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $address = UserAddress::find($id);
        if (!$address) {
            return response()->json(['status' => false, 'message' => 'Address not found.'], 404);
        }

        $address->update($request->all());
        return response()->json(['status' => true, 'message' => 'Address updated successfully.']);
    }

    public function destroy($id): JsonResponse
    {
        $address = UserAddress::find($id);
        if (!$address) {
            return response()->json(['status' => false, 'message' => 'Address not found.'], 404);
        }

        $address->delete();
        return response()->json(['status' => true, 'message' => 'Address deleted successfully.']);
    }
}
