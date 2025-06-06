<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Callinglog;
use App\Models\Register;
use App\Models\Staff;
use Illuminate\Support\Facades\Validator;

class CallinglogController extends Controller
{

    public function index(){
        $call = Callinglog::all();

        return response()->json([
            'success' => true,
            'data' => $call
        ]);
    }
    private function validate()
    {
        return [
        'location_id'       => 'required|exists:registers,id',
        'staff_id'          => 'required|exists:staffs,id',
        'not_recieved'      => 'required|integer|min:0',
        'hot_client'        => 'required|integer|min:0',
        'not_required'      => 'required|integer|min:0',
        'demo'              => 'required|integer|min:0',
        'total_calling'     => 'required|integer|min:0',
        'work_remark'       => 'nullable|string',
        'support'           => 'required|integer|min:0',
        'support_remark'    => 'nullable|string',
        'installation'      => 'required|integer|min:0',
        'install_remark'    => 'nullable|string',
        'demo_given'        => 'required|integer|min:0',
        'demo_remark'       => 'nullable|string',
        ];
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->validate());

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first()
            ], 422);
        }

        try{
            $call = Callinglog::create([
                'location_id' => $request->location_id,
                'staff_id' => $request->staff_id,
                'not_recieved' => $request->not_recieved,
                'hot_client' => $request->hot_client,
                'not_required' => $request->not_required,
                'demo' => $request->demo,
                'total_calling' => $request->total_calling,
                'work_remark' => $request->work_remark,
                'support' => $request->support,
                'support_remark' => $request->support_remark,
                'installation' => $request->installation,
                'install_remark' => $request->install_remark,
                'demo_given' => $request->demo_given,
                'demo_remark' => $request->demo_remark
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Calling Log created successfully !',
                'data' => $call
            ], 201);
        } catch (\Exception $e)
        {
            return response()->json([
                'success' => false,
                'message' => 'Somthing want wrong !',
                'error' => $e->getmessage()
            ], 500);
        }
    }
    public function update(Request $request, string $id){

        $validator = Validator::make($request->all(), $this->validate());

        if($validator->fails()){
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->first()
            ], 422);
        }

        try {
            $call = Callinglog::findorFail($id);

            $call->update([
                'location_id' => $request->location_id,
                'staff_id' => $request->staff_id,
                'not_recieved' => $request->not_recieved,
                'hot_client' => $request->hot_client,
                'not_required' => $request->not_required,
                'demo' => $request->demo,
                'total_calling' => $request->total_calling,
                'work_remark' => $request->work_remark,
                'support' => $request->support,
                'support_remark' => $request->support_remark,
                'installation' => $request->installation,
                'install_remark' => $request->install_remark,
                'demo_given' => $request->demo_given,
                'demo_remark' => $request->demo_remark
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Call Log updated successfully !',
                'data' => $call
            ], 200);
        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Somthing want wrong !',
                'error' => $e->getmessage()
            ], 500);
        }
    }
    public function destroy(string $id){
        try{
            $call = Callinglog::findorFail($id);

            $call->delete();

            return response()->json([
                'success' => true,
                'message' => 'Calling Log deleted successfully !'
            ], 201);
        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Somthing want wrong !',
                'error' => $e->getmessage()
            ], 500);
        }
    }
}
