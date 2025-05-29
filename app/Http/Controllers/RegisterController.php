<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Register;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class RegisterController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
        'company_name'   => 'required|string|max:255',
        'owner_name'     => 'required|string|max:255',
        'business_type'  => 'required|string|max:255',
        'address'        => 'required|string|max:500',
        'phone_no'       => 'required|numeric|digits_between:7,15|unique:register,phone_no',
        'email'       => 'required|email|unique:register,email',
        'password'       => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()], 422);
        }
        try{
            $register = Register::create([
                'company_name' => $request->company_name,
                'owner_name' => $request->owner_name,
                'business_type' => $request->business_type,
                'address' => $request->address,
                'phone_no' =>$request->phone_no,
                'email' => $request->email,
                'password' => $request->password
            ]);
            $user = User::create([
                'name' => $register->owner_name,
                'email' => $register->email,
                'password' => $register->password,
                'role' => 'admin'
            ]);
            return response()->json([
                'success' => true,
                'message' => 'register successfully'
            ],201);
            
        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Somthing want wrong',
                'error' => $e->getmessage()
            ],500);
        }
    }
    public function user(Request $request)
    {
        $validator = Validator::make($request->all(),[
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users,email',
        'password' => 'required|string|min:6|confirmed', // optional: use password_confirmation
        'role'     => 'nullable|in:staff',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()], 422);
        }
        try{
            $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'role' => 'staff'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'user add successfully'
            ],201);

        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Somthing want wrong',
                'error' => $e->getmessage()
            ],500);
        }
    }
    
}
