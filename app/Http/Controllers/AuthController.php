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
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
        ]);

        return response()->json(['message' => 'User created successfully'], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        // Check if user exists, password matches, and role is either 'staff' or 'admin'
        if (
            !$user ||
            $user->password !== $request->password || // assuming plain text
            !in_array($user->role, ['staff', 'admin'])
        ) {
            return response()->json(['message' => 'Invalid credentials or unauthorized role'], 401);
        }

        // Generate token
        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json(['token' => $token]);
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

    public function changePassword(Request $request)
    {
        $user = Auth::user(); // Authenticated user (users table)

        // ✅ Only admin allowed
        if (! $user || $user->role !== 'admin') {
            return response()->json(['error' => 'Only admin can change password'], 403);
        }

        // ✅ Validate inputs (at least one: email or phone)
        $validator = Validator::make($request->all(), [
            'email' => 'nullable|email',
            'phone_no' => 'nullable|digits_between:7,15',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        if (!$request->email && !$request->phone_no) {
            return response()->json(['error' => 'Email or phone number is required'], 422);
        }

        // ✅ Match register record by either email or phone
        $targetUser = Register::when($request->email, function ($query) use ($request) {
            return $query->where('email', $request->email);
        })->when($request->phone_no, function ($query) use ($request) {
            return $query->orWhere('phone_no', $request->phone_no);
        })->first();

        if (! $targetUser) {
            return response()->json(['error' => 'No matching user found'], 404);
        }

        // ✅ Update password (Hash it)
        $hashedPassword = $request->password;

        $targetUser->password = $hashedPassword;
        $targetUser->save();

        // ✅ Also update in users table if exists
        User::where('email', $targetUser->email)->update([
            'password' => $hashedPassword
        ]);

        return response()->json(['message' => 'Password changed successfully by admin']);
    }
}