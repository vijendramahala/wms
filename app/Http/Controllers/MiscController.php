<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Misc;
use App\Models\Register;
use Illuminate\Support\Facades\Validator;

class MiscController extends Controller
{
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'location_id' => 'required|exists:registers,id',
            'external_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->first()
            ], 422);
        }

        $misc = Misc::where('location_id', $request->location_id)
                    ->where('external_id', $request->external_id)
                    ->get();

        return response()->json([
            'success' => true,
            'data' => $misc
        ], 200);
    }

    private function validate()
    {
        return [
        'location_id'   => 'required|exists:registers,id',
        'external_id' => 'required|integer',
        'name' => 'required|string|max:255'
        ];
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(),$this->validate());

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->first()
            ], 422);
        }

        try{
            $misc = Misc::create([
                'location_id' => $request->location_id,
                'external_id' => $request->external_id,
                'name' => $request->name
            ]);
            return response()->json([
                'success' => true,
                'message' => ' recode created successfully !',
                'data' => $misc
            ], 201);
        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Somthing want wrong',
                'error' => $e->getmessage()
            ], 500);
        } 
    }
    public function update(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), $this->validate());

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->first()
            ], 422);
        }
        try{
            $misc = Misc::findorFail($id);

            $misc->update([
                'location_id' => $request->location_id,
                'external_id' => $request->external_id,
                'name' => $request->name
            ]);
            return response()->json([
                'success' => true,
                'message' => "updated successfully !",
                'data' => $misc
            ], 200);
        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Somthing want wrong !',
                'error' => $e->getmessage()
            ], 500);
        }
    }
    public function destroy(string $id)
    {
        try{
            $misc = Misc::findorFail($id);

            $misc->delete();

            return response()->json([
                'success' => true,
                'message' => 'deleted successfylly !'
            ], 200);
        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Somthing want wrong !',
                'error' => $e->getmessage()
            ], 500);
        }
    }

}
