<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validatedFields = $request->validate([
            'name' => 'required|string|max:50',
            'email' => 'required|email|unique:users',
            'password' => 'required|confirmed',
            'role' => ['required', Rule::in(['employer', 'jobseeker'])],
        ]);

        $role = Role::where('name', $validatedFields['role'])->first();

        if (!$role) {
            return response()->json([
                'message' => 'Role not found.',
            ], 422);
        }

        $user = User::create([
            'name' => $validatedFields['name'],
            'email' => $validatedFields['email'],
            'password' => Hash::make($validatedFields['password']),
            'role' => $validatedFields['role'],
            'role_id' => $role->id,
        ]);

        $token = $user->createToken('token')->plainTextToken;

        return response()->json([
            'message' => 'User created successfully.',
            'user' => $user,
            'token' => $token,
        ]);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:users',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return [
                'errors' => [
                    'email' => ['The provided credentials are incorrect.']
                ]
            ];
        }
        $token = $user->createToken('token')->plainTextToken;

        return [
            'message' => 'Login successful.',
            'user' => $user,
            'token' => $token,
        ];
    }

    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return [
            'message' => 'You are logged out.'
        ];
    }

}
