<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Staff;
use App\Models\Register;
use App\Models\Reminder;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ReminderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reminder = Reminder::with(['register', 'staff'])->get();

    return response()->json([
        'status' => true,
        'data' => $reminder
    ]);
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
        'location_id'    => 'required|exists:registers,id',
        'staff_id'       => 'required|exists:staffs,id',
        'title'          => 'required|string|max:255',
        'reminder_date'  => 'required|date',
        'reminder_time'  => 'required|date_format:H:i',
        ];
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),$this->validate());

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()->first()],422);
        }
        try{
            $reminder = Reminder::create([
                'location_id'   => $request->location_id,
                'staff_id'      => $request->staff_id,
                'title'         => $request->title,
                'reminder_date' => $request->reminder_date,
                'reminder_time' => $request->reminder_time
            ]);
            $reminder = $reminder->load(['register','staff']);

            return response()->json([
                'success' => true,
                'message' => 'Reminder add successfully',
                'data' => $reminder
            ],201);
        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Somthing want wrong',
                'error' => $e->getmessage()
            ],500);
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
        $validator = Validator::make($request->all(),$this->validate());

        if($validator->fails()){
            return response()->json(['errors' => $validator->errors()->first()],422);
        }
        try{
            $reminder = Reminder::findorFail($id);

            $reminder->update([
                'location_id'   => $request->location_id,
                'staff_id'      => $request->staff_id,
                'title'         => $request->title,
                'reminder_date' => $request->reminder_date,
                'reminder_time' => $request->reminder_time
            ]);
            $reminder = $reminder->load(['register','staff']);

            return response()->json([
                'success' => true,
                'message' => 'Reminder update successfully',
                'data' => $reminder
            ],200);

        }catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Somthing want wrong',
                'error' => $e->getmessage()
            ],500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try{
            $reminder = Reminder::findorFail($id);
            $reminder->delete();

            return response()->json([
                'success' => true,
                'message' => 'Deleted successfully!',
            ]);
        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Somthing want wrong',
                'error' => $e->getmessage()
            ],500);
        }
    }

    public function filterByDate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'from' => 'required|date',
            'to'   => 'required|date|after_or_equal:from',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $user = auth()->user(); // Logged-in user
            if ($user->role === 'staff') {
                $staff = Staff::where('user_id', $user->id)->first();
                if (!$staff) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Staff profile not found for this user.'
                    ], 404);
                }

                $reminders = Reminder::where('staff_id', $staff->id)
                    ->whereBetween('reminder_date', [$request->from, $request->to])
                    ->get();

            } else {
                // For admin: return all reminders in date range (optional)
                $reminders = Reminder::whereBetween('reminder_date', [$request->from, $request->to])
                    ->get();
            }

            return response()->json([
                'success' => true,
                'data' => $reminders
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
