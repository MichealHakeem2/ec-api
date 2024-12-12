<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Validator;

class RoleController extends Controller
{
    public function index(): JsonResponse
    {
        $roles = Role::all();
        return response()->json($roles);
    }

    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255|unique:roles',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first()
            ], Response::HTTP_BAD_REQUEST);
        }

        $role = Role::create($request->all());
        return response()->json([
            'status' => true,
            'data' => $role,
            'message' => 'Role created successfully.'
        ], Response::HTTP_CREATED);
    }

    public function show($id): JsonResponse
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['status' => false, 'message' => 'Role not found.'], 404);
        }
        return response()->json(['status' => true, 'data' => $role]);
    }

    public function update(Request $request, $id): JsonResponse
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['status' => false, 'message' => 'Role not found.'], 404);
        }

        $role->update($request->all());
        return response()->json(['status' => true, 'message' => 'Role updated successfully.']);
    }

    public function destroy($id): JsonResponse
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json(['status' => false, 'message' => 'Role not found.'], 404);
        }

        $role->delete();
        return response()->json(['status' => true, 'message' => 'Role deleted successfully.']);
    }
}
