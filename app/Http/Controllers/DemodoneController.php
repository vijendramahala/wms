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

            // Find Prospect to get product
            $prospect = Prospect::where('prospect_name', $request->prospect_name)->first();
            if (!$prospect) {
                return response()->json([
                    'success' => false,
                    'message' => 'Prospect not found.'
                ], 404);
            }

            // Save record to staff_prospect_reports
            $report = Demodone::create([
                'location_id' => $request->location_id,
                'staff_id' => $senderStaff->id,
                'date' => $request->date,
                'prospect_name' => $request->prospect_name,
                'product' => $prospect->product,
                'staff_name' => $request->staff_name, // ✅ Receiver name
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


}
