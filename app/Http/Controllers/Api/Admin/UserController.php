<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(['success' => true, 'data' => User::with('roles')->paginate(20)]);
    }

    public function show(int $id): JsonResponse
    {
        return response()->json(['success' => true, 'data' => User::with('roles')->findOrFail($id)]);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:8'],
            'role'     => ['required', 'in:admin,seller,buyer,driver'],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'password' => Hash::make($request->password),
        ]);
        $user->assignRole($request->role);

        return response()->json(['success' => true, 'data' => $user], 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $user = User::findOrFail($id);
        $user->update($request->only(['name', 'phone']));
        if ($request->role) $user->syncRoles([$request->role]);

        return response()->json(['success' => true, 'data' => $user]);
    }

    public function destroy(int $id): JsonResponse
    {
        User::findOrFail($id)->delete();
        return response()->json(['success' => true, 'message' => 'User dihapus.']);
    }
}
