<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Demodone;
use App\Models\Staff;
use App\Models\Prospect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DemodoneController extends Controller
{
    // Step 1: Staff A creates task and assigns to Staff B
    public function forwardProspect(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'prospect_name' => 'required|string|exists:Prospects,prospect_name',
            'staff_name' => 'required|string|exists:staffs,staff_name',
            'location_id' => 'required|exists:registers,id',
            'date' => 'required|date'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first()
            ], 422);
        }

        try {
            $sender = Auth::user();
            $senderStaff = Staff::where('user_id', $sender->id)->first();

            if (!$senderStaff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Sender staff not found.'
                ], 404);
            }

            $prospect = Prospect::where('prospect_name', $request->prospect_name)->first();
            if (!$prospect) {
                return response()->json([
                    'success' => false,
                    'message' => 'Prospect not found.'
                ], 404);
            }

            $report = Demodone::create([
                'location_id' => $request->location_id,
                'staff_id' => $senderStaff->id, // Staff A
                'date' => $request->date,
                'prospect_name' => $request->prospect_name,
                'product' => $prospect->product,
                'staff_name' => $request->staff_name // Staff B
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Prospect forwarded to staff.',
                'data' => $report
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Step 2: Staff B forwards task back to Staff A
    public function forwardBackToCreator(Request $request, $id)
    {
        try {
            $user = Auth::user();
            $staff = Staff::where('user_id', $user->id)->first();

            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff not found.'
                ], 404);
            }

            $task = Demodone::find($id);
            if (!$task) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task not found.'
                ], 404);
            }

            if (strtolower(trim($task->staff_name)) !== strtolower(trim($staff->staff_name))) {
                return response()->json([
                    'success' => false,
                    'message' => 'You are not authorized to forward this task.'
                ], 403);
            }

            $creator = Staff::find($task->staff_id);
            if (!$creator) {
                return response()->json([
                    'success' => false,
                    'message' => 'Original creator not found.'
                ], 404);
            }

            $task->assigned_to = $creator->staff_name;
            $task->save();

            return response()->json([
                'success' => true,
                'message' => 'Task sent back to original staff successfully.',
                'data' => $task
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    // Step 3: Only show tasks assigned to me
    public function myTasks()
    {
        $user = Auth::user();
        $staff = Staff::where('user_id', $user->id)->first();

        if (!$staff) {
            return response()->json([
                'success' => false,
                'message' => 'Staff not found.'
            ], 404);
        }

        $myName = strtolower(trim($staff->staff_name));

        $tasks = Demodone::whereRaw('LOWER(TRIM(staff_name)) = ?', [$myName])->get();

        return response()->json([
            'success' => true,
            'data' => $tasks
        ]);
    }

    // Step 4: Staff A completes task after it's returned
    public function completeTask(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'price' => 'nullable|string',
            'amc' => 'nullable|string',
            'licence_no' => 'nullable|string',
            'mobile_no' => 'nullable|string'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first()
            ], 422);
        }

        try {
            $task = Demodone::find($id);
            if (!$task) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task not found.'
                ], 404);
            }

            $user = Auth::user();
            $staff = Staff::where('user_id', $user->id)->first();

            if (!$staff) {
                return response()->json([
                    'success' => false,
                    'message' => 'Staff not found.'
                ], 404);
            }

            // ✅ Only creator can complete
            if ($staff->id !== $task->staff_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Only the original creator can complete this task.'
                ], 403);
            }

            $task->update([
                'price' => $request->price,
                'amc' => $request->amc,
                'licence_no' => $request->licence_no,
                'mobile_no' => $request->mobile_no,
                'assigned_to' => null // task now complete
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Task completed successfully.',
                'data' => $task
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}