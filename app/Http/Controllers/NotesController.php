<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;
use App\Models\staff;
use App\Models\Register;
use Illuminate\Support\Facades\Validator;

class NotesController extends Controller
{
    private function validateRules(){
        return [
        'location_id'      => 'required|exists:registers,id',
        'staff_id'         => 'required|exists:staffs,id',
        'title'            => 'required|string|max:255',
        'note'             => 'nullable|string',
        'background_color' => 'nullable|string|max:50',
        'task'             => 'required|array|min:1',
        'task.*.text'      => 'required|string|max:255',
        'task.*.done'      => 'required|boolean',
        'pin_status'       => 'required|boolean',
            
        ];
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),$this->validateRules());

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->first()
            ],422);
        }
        try{
            $note = Note::create([
               'location_id' => $request->location_id,
               'staff_id' => $request->staff_id,
               'title' => $request->title,
               'note' => $request->note,
               'background_color' => $request->background_color,
               'task' => json_encode($request->task),
               'pin_status' => $request->pin_status
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Note created successfully!',
                'data' => $note
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
        $validator = Validator::make($request->all(),$this->validateRules());

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->first()
            ],422);
        }
        try{
            $note = Note::findorFail($id);

            $note->update([
                'location_id' => $request->location_id,
               'staff_id' => $request->staff_id,
               'title' => $request->title,
               'note' => $request->note,
               'background_color' => $request->background_color,
               'task' => json_encode($request->task),
               'pin_status' => $request->pin_status
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Note update successfully!',
                'data' => $note
            ],200);
        } catch (\Exception $e){
            return response()->json([
            'success' => false,
            'message' => 'Somthing want wrong',
            'error' => $e->getmessage()
            ],500);
        }
    }
    public function getByLocationAndStaff(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location_id' => 'required|exists:registers,id',
            'staff_id'    => 'required|exists:staffs,id',
        ]);

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->first()
            ],422);
        }


        try {
            $notes = Note::where('location_id', $request->location_id)
                        ->where('staff_id', $request->staff_id)
                        ->get();

            if ($notes->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No data found for this location and staff combination.'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'data'    => $notes
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
        try {
            $note = Note::findorFail($id);
            $note->delete();

            return response()->json([
                'success' => true,
                'message' => 'Deleted successfully!'
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
}
