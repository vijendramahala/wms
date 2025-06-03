<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Leave;
use App\Models\Staff;
use App\Models\Register;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class LeaveController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only admin can access this data.'
            ], 403);
        }

        // Validate location_id
        $request->validate([
            'location_id' => 'required|exists:registers,id',
        ]);

        // Get all leave records by location_id
        $leaves = Leave::where('location_id', $request->location_id)->get();

        // Format response: extract 'category' from 'reason'
        $data = $leaves->map(function ($leave) {
            $reason = json_decode($leave->reason, true); // decode reason JSON
            return [
                'id'            => $leave->id,
                'staff_id'      => $leave->staff_id,
                'apply_date'    => $leave->apply_date,
                'from_date'     => $leave->from_date,
                'to_date'       => $leave->to_date,
                'total_days'    => $leave->total_days,
                'category'      => $reason['category'] ?? null, // only category
                'approve_status'=> $leave->approve_status,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Leave records fetched successfully.',
            'data'    => $data
        ], 200);
    }

    private function validate()
    {
        return [
        'location_id'   => 'required|exists:registers,id',
        'staff_id'      => 'required|exists:staffs,id',
        'apply_date'    => 'required|date',
        'from_date'     => 'required|date|after_or_equal:apply_date',
        'to_date'       => 'required|date|after_or_equal:from_date',
        'total_days'    => 'nullbel|integer|min:1',
        'reason'        => 'required|array', // reason is JSON
        'reason.date'   => 'required|date',
        'reason.category' => 'required|string|max:255',
        'approve_status' => 'required|string|in:pending,approved,rejected',
        ];
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        if(!$user || $user->role !== 'staff'){
            return response()->json([
                'success' => false,
                'message' => "Only staff can perform this action."
            ], 403);
        }


        $validator = Validator::make($request->all(),$this->validate());

        if($validator->fails())
        {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->first()
            ], 422);
        }

        try{
            $leave = Leave::create([
            'location_id'     => $request->location_id,
            'staff_id'        => $request->staff_id,
            'apply_date'      => $request->apply_date,  // also fix key name
            'from_date'       => $request->from_date,
            'to_date'         => $request->to_date,
            'total_days'      => $request->total_days,
            'reason'          => json_encode($request->reason),  // ✅ correct
            'approve_status'  => $request->approve_status
        ]);

            return response()->json([
                'success' => true,
                'message' => 'Leave aplication created successfully !',
                'data' => $leave
            ], 201);
        } catch (\Exception $e)
        {
            return response()->json([
                'success' => false,
                'message' => 'Somthing want wrong',
                'error' => $e->getmessage()
            ], 500);
        }
    }
    public function update(Request $request, string $id)
    {
        $user = Auth::user();

        if(!$user || $user->role !== 'admin')
        {
            return response()->json([
                'success' => false,
                'message' => 'Only admin can perform this action.'
            ], 403);
        }

        $validator = Validator::make($request->all(),$this->validate());

        if($validator->fails())
        {
            return response()->json([
                'success' => false,
                'errors' => $Validator->errors()->first()
            ], 422);
        }
        try {
            $leave = Leave::findorFail($id);

            $leave->update([
                'location_id' => $request->location_id,
                'staff_id' => $request->staff_id,
                'apply_date' => $request->apply_date,
                'from_date' => $request->from_date,
                'to_date' => $request->to_date,
                'total_days' => $request->total_days,
                'reason' => json_encode($request->reason),
                'approve_status' => $request->approve_status
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Leave aplication updated successfully !',
                'data' => $leave,
            ], 200);
        }catch (\Exceptin $e){
            return response()->json([
                'success' => false,
                'message' => 'Somthing want wrong',
                'error' => $e->getmessage()
            ], 500);
        }
    }
    public function staffLeaves()
    {
        $user = Auth::user();

        if (!$user || $user->role !== 'staff') {
            return response()->json([
                'success' => false,
                'message' => 'Only staff can access this data.'
            ], 403);
        }

        // Staff ID match karega user se
        $staff = Staff::where('user_id', $user->id)->first();

        if (!$staff) {
            return response()->json([
                'success' => false,
                'message' => 'Staff record not found for this user.'
            ], 404);
        }

        // Get staff-specific leave records
        $leaves = Leave::where('staff_id', $staff->id)->get();

        $data = $leaves->map(function ($leave) {
            $reason = json_decode($leave->reason, true);
            return [
                'id'            => $leave->id,
                'apply_date'    => $leave->apply_date,
                'from_date'     => $leave->from_date,
                'to_date'       => $leave->to_date,
                'total_days'    => $leave->total_days,
                'category'      => $reason['category'] ?? null,
                'approve_status'=> $leave->approve_status,
            ];
        });

        return response()->json([
            'success' => true,
            'message' => 'Your leave records fetched successfully.',
            'data'    => $data
        ], 200);
    }

}
