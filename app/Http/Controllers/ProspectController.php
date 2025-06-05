<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prospect;
use App\Models\Staff;
use App\Models\Register;
use App\Models\ProspectHistory;
use App\Models\Misc;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class ProspectController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->role === 'staff') {
            $staff = staff::where('user_id', $user->id)->first();

            if(!$staff){
                return response()->json([
                    'success' => false,
                    'message' => 'staff recode not found.'
                ], 404);
            }

            $prospect = prospect::where('staff_id', $staff->id)->get();

            return response()->json([
                'success' => true,
                'data' => $prospect,
            ], 200);
        }

        if($user->role === 'admin') {
            $prospect = Prospect::all();

            return response()->json([
                'success' => true,
                'data' => $prospect,
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Unauthorized',
        ], 403);

    }


    private function validate()
    {
        return [
        'location_id'      => 'required|exists:registers,id',
        'staff_id'         => 'required|exists:staffs,id',
        'priority'         => 'required|string|max:50',
        'prospect_name'    => 'required|string|max:255',
        'mobile_no'        => 'required|string|max:15',
        'alternative_no'   => 'nullable|string|max:15',
        'city'             => 'nullable|string|max:100',
        'district'         => 'nullable|string|max:100',
        'state'            => 'nullable|string|max:100',
        'address'          => 'required|string|max:255',
        'product'          => 'required|string|max:100',
        'variant'          => 'required|string|max:100',
        'software_price'   => 'required|numeric|min:0|max:99999999.99',
        'date'             => 'required|date',
        'time'             => 'required|date_format:H:i',
        'remark'           => 'required|string',
        'demo_details'     => 'required|string',
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
            $prospect = Prospect::create([
                'location_id' => $request->location_id,
                'staff_id' => $request->staff_id,
                'priority' => $request->priority,
                'prospect_name' => $request->prospect_name,
                'mobile_no' => $request->mobile_no,
                'alternative_no' => $request->alternative_no,
                'city' => $request->city,
                'district' => $request->district,
                'state' => $request->state,
                'address' => $request->address,
                'product' => $request->product,
                'variant' => $request->variant,
                'software_price' => $request->software_price,
                'date' => $request->date,
                'time' => $request->time,
                'remark' => $request->remark,
                'demo_details' => $request->demo_details,
            ]);
            return response()->json([
                'sucess' => true,
                'message' => 'prospect created successfully !',
                'data' => $prospect
            ], 201);
        } catch (\Excetion $e)
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
        $validator = Validator::make($request->all(), $this->validate());

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors()->first()
            ], 422);
        }

        try {
            $prospect = Prospect::findOrFail($id);

            $prospect->update([
                'location_id' => $request->location_id,
                'staff_id' => $request->staff_id,
                'priority' => $request->priority,
                'prospect_name' => $request->prospect_name,
                'mobile_no' => $request->mobile_no,
                'alternative_no' => $request->alternative_no,
                'city' => $request->city,
                'district' => $request->district,
                'state' => $request->state,
                'address' => $request->address,
                'product' => $request->product,
                'variant' => $request->variant,
                'software_price' => $request->software_price,
                'date' => $request->date,
                'time' => $request->time,
                'remark' => $request->remark,
                'demo_details' => $request->demo_details,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Prospect updated successfully!',
                'data' => $prospect
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong!',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy(string $id){
        
        try{
            $prospect = Prospect::findorFail($id);

            $prospect->delete();

            return response()->json([
                'success' => true,
                'message' => 'prospect deleted successfully !',
            ], 201);
            
        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Somthing want wrong',
                'error' => $e->getmessage()
            ], 500);
        }
    }
    public function history($prospectId)
    {
        $history = ProspectHistory::where('prospect_id', $prospectId)
                    ->with('user') // user relation agar define kiya ho
                    ->get();

        return response()->json([
            'success' => true,
            'data' => $history
        ]);
    }

    public function filterbycreate_at(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from'
        ]);
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $user = Auth::user();
            if($user->role === 'staff'){
                $staff = staff::where('user_id', $user->id)->first();

                if(!$user){
                    return response()->json([
                        'success' => false,
                        'message' => 'Staff profile not found for this user'
                    ], 404);
                }

                $prospect = Prospect::where('staff_id', $staff->id)
                            ->whereBetween('created_at', [$request->from, $request->to])
                            ->get();
            } else {
                $prospect = Prospect::whereBetween('created_at',[$request->from,$request->to])->get();
            }
            return response()->json([
                'success' => true,
                'data' => $prospect 
            ]);
        } catch (\Exception $e){
            return response()->json([
                'success' => false,
                'message' => 'Somthing want wrong',
                'error' => $e->getmessage()
            ], 500);
        }
    }

    public function filterbydate(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from'
        ]);
        if($validator->fails()){
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()->first(),
            ], 422);
        }

        try {
            $user = Auth::user();

            if($user->role === 'staff'){
                $staff = staff::where('user_id', $user->id)->first();

                if(!$user){
                    return response()->json([
                        'success' => false,
                        'message' => 'Staff profile not found for this user.'
                    ], 404);
                }

                $prospect = Prospect::where('staff_id', $staff->id)
                                    ->whereBetween('date',[$request->from, $request->to])
                                    ->get();
            } else {
                $prospect = Prospect::whereBetween('date', [$request->from, $request->to])->get();
            }
            return response()->json([
                'success' => true,
                'data' => $prospect
            ]);
        } catch (\Exception $e)
        {
            return response()->json([
                'success' => false,
                'message' => 'Somthing want wrong',
                'error' => $e->getmessage()
            ], 500);
        }
    }
    public function getByPriority(Request $request)
    {
        try {
            $user = Auth::user();

            if ($user->role === 'staff') {
                $staff = staff::where('user_id', $user->id)->first();

                if (!$staff) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Staff profile not found for this user.'
                    ], 404);
                }

                $hotProspects = Prospect::where('staff_id', $staff->id)
                                        ->where('priority', 'Hot')
                                        ->get();

                $coldProspects = Prospect::where('staff_id', $staff->id)
                                        ->where('priority', 'Cold')
                                        ->get();

                $normalProspects = Prospect::where('staff_id', $staff->id)
                                        ->where('priority', 'Normal')
                                        ->get();
            } else {
                // Admin
                $hotProspects = Prospect::where('priority', 'Hot')->get();
                $coldProspects = Prospect::where('priority', 'Cold')->get();
                $normalProspects = Prospect::where('priority', 'Normal')->get();
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'hot' => $hotProspects,
                    'cold' => $coldProspects,
                    'normal' => $normalProspects
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getAllSoftwareWithProspects()
    {
        try {
            $user = Auth::user();

            $softwares = Misc::pluck('name');

            $result = [];

            foreach ($softwares as $software) {
                if ($user->role === 'staff') {
                    $staff = staff::where('user_id', $user->id)->first();

                    if (!$staff) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Staff profile not found for this user.'
                        ], 404);
                    }

                    $prospects = Prospect::where('staff_id', $staff->id)
                                        ->where('product', $software)
                                        ->get();
                } else {
                    
                    $prospects = Prospect::where('product', $software)->get();
                }

                $result[$software] = $prospects;
            }

            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage()
            ], 500);
        }
    }


}
