<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\staff;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class StaffController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $staff = staff::all();
        {
            return response()->json([
                'success' => true,
                'data' => $staff
            ],200);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }
    private function validate(){
        return [
            'staff_name' => 'required|string|max:255',
            'father_name' => 'required|string|max:255',
            'mobile_no' => 'required|digits:10',
            'pin_code' => 'required|digits:6',
            'state' => 'required|string|max:100',
            'city' => 'required|string|max:100',
            'address' => 'required|string|max:500',
            'sales_man' => 'required|string|max:255',
            'sales_executive' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'joining_date' => 'required|date',
            'resignation_date' => 'nullable|date|after_or_equal:joining_date',
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), $this->validate());

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()->first()], 422);
    }

    try {
        // ✅ Get Admin User for email
        $adminUser = User::where('role', 'admin')->first();
        if (!$adminUser) {
            return response()->json([
                'success' => false,
                'message' => 'Admin user not found to assign email.',
            ], 404);
        }

        // ✅ First create User (login)
        $user = User::create([
            'name'     => $request->staff_name,
            'email'    => $adminUser->email, // Shared email
            'password' => $request->password, // ✅ Hash password
            'role'     => 'staff',
        ]);

        // ✅ Then create Staff (linked to user)
        $staff = staff::create([
            'staff_name'        => $request->staff_name,
            'father_name'       => $request->father_name,
            'mobile_no'         => $request->mobile_no,
            'pin_code'          => $request->pin_code,
            'state'             => $request->state,
            'city'              => $request->city,
            'address'           => $request->address,
            'sales_man'         => $request->sales_man,
            'sales_executive'   => $request->sales_executive,
            'password'          => $request->password, // ✅ Hash again for safety
            'joining_date'      => $request->joining_date,
            'resignation_date'  => $request->resignation_date,
            'user_id'           => $user->id, // ✅ now user_id is available
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Staff created successfully',
            'data'    => $staff,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Something went wrong',
            'error'   => $e->getMessage()
        ], 500);
    }
}




    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
   public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), $this->validate());

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()->first()], 422);
        }
        // dd($request->resignation_date);


        try {
            $adminUser = User::where('role', 'admin')->first();

            if (!$adminUser) {
                return response()->json([
                    'success' => false,
                    'message' => 'Admin user not found to assign email.'
                ], 404);
            }

            $staff = Staff::findOrFail($id);

           $resignationDate = $request->has('resigned') && $request->resigned ? now() : null;

            $staff->update([
                'staff_name'        => $request->staff_name,
                'father_name'       => $request->father_name,
                'mobile_no'         => $request->mobile_no,
                'pin_code'          => $request->pin_code,
                'state'             => $request->state,
                'city'              => $request->city,
                'address'           => $request->address,
                'sales_man'         => $request->sales_man,
                'sales_executive'   => $request->sales_executive,
                'password'          => $request->password,
                'joining_date'      => $request->joining_date,
                'resignation_date'  => $resignationDate,
            ]);


            // Assuming staff table has user_id linking to users table
            $user = User::findOrFail($staff->user_id);

            $user->update([
                'name'     => $staff->staff_name,
                'email'    => $adminUser->email,
                'password' => $staff->password,
                'role'     => 'staff',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Staff updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $staff = staff::findorFail($id);
            $user = user::findorFail($staff->user_id);

            $staff->delete();
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Deleted successfully'
            ]);
        } catch (\Exeption $e){
            return response()->json([
                'success' => false,
                'message' => 'Somthing want wrong',
                'error' => $e->getmessage()
            ],500);
        }
    }
}
