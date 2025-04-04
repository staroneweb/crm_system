<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Opportunities;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class OpportunitieController extends Controller
{
    public function opportunitieAdd(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'opportunity_name' => 'required|string|max:255',
                'expected_close_date' =>'required',
                'opp_amount'=>'required',
                'probability'=>'required',
                'description'=>'required'

            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 422, 'message' => $validator->errors()]);
            }

            $expected_close_date = Carbon::createFromFormat('d-m-Y', $request->expected_close_date)->format('Y-m-d');

            $ooprtuniti = new Opportunities();
            $ooprtuniti->opportunity_name = $request->opportunity_name;
            $ooprtuniti->expected_close_date = $expected_close_date;
            $ooprtuniti->opp_amount = $request->opp_amount;
            $ooprtuniti->probability = $request->probability;
            $ooprtuniti->description = $request->description;
            $ooprtuniti->lead_id = $request->lead_id;
            $ooprtuniti->assigned_to =$request->assigned_to ?? null;


            if ($ooprtuniti->save()) {

                return response()->json(['status' => 200, 'message' => 'Ooprtuniti Add Successfully!']);
            } else {

                return response()->json(['status' => 500, 'message' => 'Ooprtuniti Add Failed!']);
            }
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while Ooportuniti add. Please try again later.']);
        }
    }
    public function opportunitieEdit(Request $request)
    {
        try {

            $opportunity_data = Opportunities::with(['assignedUser'])->where('id', $request->opportunity_id)->first();

            if (!$opportunity_data) {

                return response()->json(['status' => 200, 'message' => 'Data Not Found!']);
            }

            $data[] = [

                'id' => $opportunity_data->id,
                'opportunity_name' => $opportunity_data->opportunity_name,
                'expected_close_date' => Carbon::parse($opportunity_data->expected_close_date)->format('d-m-Y'),
                'status' => $opportunity_data->status,
                'opp_amount' => $opportunity_data->opp_amount,
                'assigned_to' => $opportunity_data->assignedUser->name??null,
                'description' => $opportunity_data->description,
               
            ];

            return response()->json(['status' => 200, 'message' => 'data get successfully', 'data' => $data]);
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while Ooportuniti edit. Please try again later.']);
        }
    }
    public function opportunitieUpdate(Request $request)
    {
        try {
            $opportunity_data = Opportunities::where('id', $request->opportunity_id)->first();

            if (!$opportunity_data) {

                return response()->json(['status' => 200, 'message' => 'Data Not Found!']);
            }

            $validator = Validator::make($request->all(), [
                'opportunity_name' => 'required|string|max:255',
                'expected_close_date' =>'required',
                'opp_amount'=>'required',
                'probability'=>'required',
                'description'=>'required',
                'status'      => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 422, 'message' => $validator->errors()]);
            }

            $expected_close_date = Carbon::createFromFormat('d-m-Y', $request->expected_close_date)->format('Y-m-d');

            $opportunity_data->opportunity_name = $request->opportunity_name;
            $opportunity_data->expected_close_date = $expected_close_date;
            $opportunity_data->opp_amount = $request->opp_amount;
            $opportunity_data->probability = $request->probability;
            $opportunity_data->description = $request->description;
            $opportunity_data->status = $request->status;
            $opportunity_data->lead_id = $request->lead_id;
            $opportunity_data->assigned_to =$request->assigned_to ?? null;


            if ($opportunity_data->save()) {

                return response()->json(['status' => 200, 'message' => 'Ooportuniti Update Successfully!']);
            } else {

                return response()->json(['status' => 500, 'message' => 'Ooportuniti Update Failed!']);
            }
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while ooportuniti update. Please try again later.']);
        }
    }
    public function opportunitieDelete(Request $request)
    {
        try {

            $opportunity_data = Opportunities::where('id', $request->opportunity_id)->first();

            if (!$opportunity_data) {

                return response()->json(['status' => 200, 'message' => 'Data Not Found!']);
            }

            if ($opportunity_data->delete()) {
                return response()->json(['status' => 200, 'message' => 'Ooportuniti Delete Sucessfully!']);
            }
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while ooportuniti delete. Please try again later.']);
        }
    }
    public function opportunitieList(Request $request)
    {

        try {
            $query = Opportunities::with(['assignedUser']);

            if (preg_match('/\d{2}-\d{2}-\d{4}/', $request->search)) {

                $date = Carbon::createFromFormat('d-m-Y', $request->search)->format('Y-m-d');

            } else {

                $date = null;
            }

            if (isset($request->search)) {

                $query->where('opportunity_name', 'like', '%' . $request->search . '%')
                ->orwhere('expected_close_date',$date)
                ->orwhere('status','like', '%' . $request->search . '%')
                ->orwhere('opp_amount','like', '%' . $request->search . '%')
                ->orwhere('probability','like', '%' . $request->search . '%')
                ->orwhere('description','like', '%' . $request->search . '%')
                ->orwhereHas('assignedUser', function ($q) use ($request) {
                    $q->where('name', 'like', '%' . $request->search . '%');
                });           
            }

            $opportunitie = $query->get();

            if ($opportunitie->isEmpty()) {

                return response()->json(['status' => 200, 'message' => 'Data Not Found!']);
            }

            $opportunitie_data = [];

            foreach ($opportunitie as $op) {

                $opportunitie_data[] = [

                    'id' => $op->id,
                    'opportunity_name' => $op->opportunity_name,
                    'expected_close_date' => Carbon::parse($op->expected_close_date)->format('d-m-Y'),
                    'status' => $op->status,
                    'opp_amount' => $op->opp_amount,
                    'assigned_to' => $op->assignedUser->name??null,
                    'description' => $op->description,
                    // 'log' => "Create DateTime : " . ($st->created_at ? $st->created_at->format('d-M-Y H:i:s') : 'N/A') .
                    //         " | Last Modified DateTime : " . ($st->updated_at ? $st->updated_at->format('d-M-Y H:i:s') : 'N/A'),

                ];
            }

            return response()->json(['status' => 200, 'data' => $opportunitie_data]);
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while ooportuniti list. Please try again later.']);
        }
    }
    

}
