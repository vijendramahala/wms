<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Register;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
{
    $request->validate([
        'email'    => 'required|email',
        'password' => 'required|string',
        'name'     => 'nullable|string' // only required for staff
    ]);

    // ✅ Check if it's an admin trying to login
    $adminUser = User::where('email', $request->email)
                     ->where('role', 'admin')
                     ->first();

    if ($adminUser && $request->password === $adminUser->password) {
        $token = $adminUser->createToken('api-token')->plainTextToken;
        return response()->json(['token' => $token, 'role' => 'admin']);
    }

    // ✅ Staff login: match by email + name + role
    if (!$request->name) {
        return response()->json(['message' => 'Name is required for staff login'], 422);
    }

    $staffUser = User::where('email', $request->email)
                     ->where('name', $request->name)
                     ->where('role', 'staff')
                     ->first();

    if (!$staffUser || $request->password !== $staffUser->password) {
        return response()->json(['message' => 'Invalid staff credentials'], 401);
    }

    $token = $staffUser->createToken('api-token')->plainTextToken;
    return response()->json(['token' => $token, 'role' => 'staff']);
}

   public function logout(Request $request)
    {
        $user = auth()->user();

        if (!$user) {
            return response()->json(['message' => 'Not authenticated'], 401);
        }

        $user->tokens()->delete();

        return response()->json(['message' => 'Logged out']);
    }

}