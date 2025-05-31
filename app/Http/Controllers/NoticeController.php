<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Notice;

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
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),$this->validate());

        if($validate->fails()){
            return response()->json([
                'success' => false,
                'errors'->$validate->errors()->first()
            ],422);
        }
        try{
            $notice = Notice::create([
                'location_id' => $request->location_id,
                'staff_id'    => $request->staff_id,
                'title' => $request->title,
                'subtitle' => $request->subtitle,
                'note' => $request->note
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Notice add successfully',
                'data' => $notice
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
