<?php

namespace App\Http\Controllers;

use App\Models\AdminLog;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse; // Add this line


class AdminLogController extends Controller
{
    public function index(): JsonResponse
    {
        $logs = AdminLog::all();
        return response()->json($logs);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'admin_id' => 'required|exists:admins,id',
            'action' => 'required|string|max:255',
            'timestamp' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], Response::HTTP_BAD_REQUEST);
        }

        $log = AdminLog::create($request->all());

        return response()->json([
            'status' => true,
            'data' => $log,
            'message' => 'Log created successfully.'
        ], Response::HTTP_CREATED);
    }

    public function show($id)
    {
        $log = AdminLog::find($id);
        if (!$log) {
            return response()->json(['status' => false, 'message' => 'Log not found.'], 404);
        }

        return response()->json(['status' => true, 'data' => $log], 200);
    }

    public function destroy($id)
    {
        $log = AdminLog::find($id);
        if (!$log) {
            return response()->json(['status' => false, 'message' => 'Log not found.'], 404);
        }

        $log->delete();

        return response()->json(['status' => true, 'message' => 'Log deleted successfully.'], 200);
    }
}
