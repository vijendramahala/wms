<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Notice;
use App\Models\Staff;

class NoticeController extends Controller
{
    private function validate(){
        return [
        'location_id' => 'required|exists:registers,id',
        'staff_id'    => 'required|array|min:1',
        'staff_id.*'  => 'required|exists:staffs,id',
        'title'       => 'required|string|max:255',
        'subtitle'    => 'required|string|max:255',
        'note'        => 'nullable|string',
        ];
    }   

    public function show($id)
    {
        if(auth()->user()->role !== 'admin'){
            return response()->json([
                'success' => false,
                'message' => 'Only admin can perform this action.'
            ]);
        }
        try {
            $notice = Notice::findOrFail($id);

            // 🔓 Decode staff_id array
            $staffIds = json_decode($notice->staff_id);

            // 🔍 Fetch staff data using IDs
            $staffDetails = Staff::whereIn('id', $staffIds)->get();

            // 🛠 Replace staff_id with actual data
            $notice->staff_id = $staffDetails;

            return response()->json([
                'success' => true,
                'data' => $notice
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Notice not found',
                'error'   => $e->getMessage()
            ], 404);
        }
    }

    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only admin can perform this action'
            ], 403);
        }

        $validator = Validator::make($request->all(), $this->validate());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->first()
            ], 422);
        }

        try {
            $notice = Notice::create([
                'location_id' => $request->location_id,
                'staff_id'    => json_encode($request->staff_id), // 🔄 Encode to JSON
                'title'       => $request->title,
                'subtitle'    => $request->subtitle,
                'note'        => $request->note
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notice added successfully',
                'data'    => $notice
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage()
            ], 500);
        }
    }


    public function update(Request $request, string $id)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only admin can perform this action'
            ], 403);
        }

        $validator = Validator::make($request->all(), $this->validate());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->first()
            ], 422);
        }

        try {
            $notice = Notice::findOrFail($id);

            $notice->update([
                'location_id' => $request->location_id,
                'staff_id'    => json_encode($request->staff_id),
                'title'       => $request->title,
                'subtitle'    => $request->subtitle,
                'note'        => $request->note
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Notice updated successfully!',
                'data'    => $notice
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

    public function destroy(string $id)
    {
        if (auth()->user()->role !== 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'Only admin can perform this action'
            ], 403);
        }

        try {
            $notice = Notice::findOrFail($id);
            $notice->delete();

            return response()->json([
                'success' => true,
                'message' => 'Deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error'   => $e->getMessage()
            ], 500);
        }
    }

}
