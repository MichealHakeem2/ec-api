<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    public function index(): JsonResponse
    {
        $notifications = Notification::all();
        return response()->json($notifications);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:500',
            'user_id' => 'required|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], Response::HTTP_BAD_REQUEST);
        }

        $notification = Notification::create($request->all());
        return response()->json([
            'status' => true,
            'data' => $notification,
            'message' => 'Notification created successfully.'
        ], Response::HTTP_CREATED);
    }

    public function show($id): JsonResponse
    {
        $notification = Notification::find($id);
        if (!$notification) {
            return response()->json(['status' => false, 'message' => 'Notification not found.'], 404);
        }
        return response()->json(['status' => true, 'data' => $notification]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $notification = Notification::find($id);
        if (!$notification) {
            return response()->json(['status' => false, 'message' => 'Notification not found.'], 404);
        }

        $notification->update($request->all());
        return response()->json(['status' => true, 'message' => 'Notification updated successfully.']);
    }

    public function destroy($id): JsonResponse
    {
        $notification = Notification::find($id);
        if (!$notification) {
            return response()->json(['status' => false, 'message' => 'Notification not found.'], 404);
        }

        $notification->delete();
        return response()->json(['status' => true, 'message' => 'Notification deleted successfully.']);
    }
}
