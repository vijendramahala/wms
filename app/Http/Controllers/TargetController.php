<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Target;
use App\Models\Register;
use App\Models\Staff;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class TargetController extends Controller
{

    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'staff') {
            // Step 1: Get staff record from staff table
            $staff = Staff::where('user_id', $user->id)->first();

            // Step 2: If staff not found, return error
            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff record not found.'
                ], 404);
            }

            // Step 3: Fetch only their targets
            $targets = Target::where('staff_id', $staff->id)->get();

            return response()->json([
                'success' => true,
                'data' => $targets
            ], 200);
        }

        // Admin can view all
        if ($user->role === 'admin') {
            $targets = Target::all();

            return response()->json([
                'success' => true,
                'data' => $targets
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unauthorized.'
        ], 403);
    }


    private function validate()
    {
        return [
        'location_id'     => 'required|exists:registers,id',
        'staff_id'        => 'required|exists:staffs,id',
        'month_target'    => 'required|string|max:255',
        'month_received'  => 'required|string|max:255',
        'week_target'     => 'required|string|max:255',
        'week_received'   => 'required|string|max:255',
        ];
    }

    public function store(Request $request)
    {
        $user = Auth::user();

    if (!$user || $user->role !== 'admin') {
        return response()->json([
            'success' => false,
            'message' => 'Only admin can perform this action.'
        ], 403);
    }


        $validator = Validator::make($request->all(),$this->validate());

        if($validator->fails()){
            return response()->json(['success' => false, 'errors' => $validator->errors()->first()],422);
        }
        try{
            $target = Target::create([
                'location_id' => $request->location_id,
                'staff_id' => $request->staff_id,
                'month_target' => $request->month_target,
                'month_received' => $request->month_received,
                'week_target' => $request->week_target,
                'week_received' => $request->week_received
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Target created successfully',
                'data' => $target
            ],201);
        } catch (\Exception $e){
            return response()->json([
            'success' => false,
            'message' => 'Somthing want wrong',
            'error' => $e->getmessage()
            ],500);
        }
    }
    public function update(Request $request, string $id)
    {
        $user = Auth::user();

        if(!$user || $user->role !== 'admin'){
            return response()->json([
                'success' => false,
                'message' => 'Only admin can perform this action.'
            ],403);
        }

        $validator = Validator::make($request->all(),$this->validate());

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->first()
            ],422);
        }
        try{
            $target = Target::findorFail($id);
            
            $target->update([
                'location_id' => $request->location_id,
                'staff_id' => $request->staff_id,
                'month_target' => $request->month_target,
                'month_received' => $request->month_received,
                'week_target' => $request->week_target,
                'week_received' => $request->week_received
            ]);
            return response()->json([
                'success' => true,
                'message' => 'target updated successfully!',
                'data' => $target
            ],200);
        } catch (\Exception $e)
        {
            return response()->json([
                'success' => false,
                'message' => 'Somthing want wrong',
                'error' => $e->getmessage()
            ],500);
        }
    }
    public function destroy(string $id)
    {
        $user = Auth::user();

        if(!$user || $user->role !== 'admin'){
            return response()->json([
                'success' => false,
                'message' => 'Only admin can perform this action.'
            ], 403);
        }
        try {
            $target = Target::findorFail($id);

            $target->delete();

            return response()->json([
                'success' => true,
                'message' => 'deleted successfuly!'
            ],200);

        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Somthing want wrong',
                'error' => $e->getmessage()
            ],500);
        }


    }
}
