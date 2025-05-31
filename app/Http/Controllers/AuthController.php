<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Register;
use App\Models\staff;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
            'name'     => 'nullable|string' // only required for staff
        ]);

        // ✅ Admin Login
        $adminUser = User::where('email', $request->email)
                        ->where('role', 'admin')
                        ->first();

        if ($adminUser && $request->password === $adminUser->password) {
            $token = $adminUser->createToken('api-token')->plainTextToken;
            return response()->json(['token' => $token, 'role' => 'admin']);
        }

        // ✅ Staff Login
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

        // 🚫 Check if staff has resigned
        $staff = Staff::where('user_id', $staffUser->id)->first();
        if ($staff && $staff->resignation_date !== null) {
            return response()->json([
                'message' => 'Access denied. You are no longer an active staff member.'
            ], 403);
        }

        // ✅ If all good, create token
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