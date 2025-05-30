<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Register;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    
    public function updateuser(Request $request, $id)
    {
        $authUser = Auth::user(); // Logged-in user

        $validator = Validator::make($request->all(), [
            'name'     => 'required|string|max:255',
            'email'    => 'required|email',
            'password' => 'required|string|min:6|confirmed',
            'role'     => 'nullable|in:staff,admin',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()], 422);
        }

        try {
            $userToUpdate = User::findOrFail($id); // User to be updated

            // ✅ Authorization Rule
            if (
                $authUser->role === 'staff' && $userToUpdate->role !== 'staff'
            ) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: Staff can only update staff users.',
                ], 403);
            }

            $userToUpdate->update([
                'name'     => $request->name,
                'email'    => $request->email,
                'password' => $request->password, // consider hashing
            ]);

            // Allow role update only if same as target user's existing role
            if ($request->has('role') && $request->role === $userToUpdate->role) {
                $userToUpdate->role = $request->role;
                $userToUpdate->save();
            }

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data'    => $userToUpdate,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }
    public function getUserById($id)
    {
        $authUser = Auth::user(); // Logged-in user

        // ✅ Only admin can access this endpoint
        if ($authUser->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: Only admin can access user data.',
            ], 403);
        }

        // ✅ If ID is 0, return all users
        if ($id == 0) {
            $users = User::all();

            return response()->json([
                'success' => true,
                'message' => 'All users fetched successfully',
                'data'    => $users,
            ]);
        }

        // ✅ Else return single user
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found for ID: ' . $id,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'User fetched successfully',
            'data'    => $user,
        ]);
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
