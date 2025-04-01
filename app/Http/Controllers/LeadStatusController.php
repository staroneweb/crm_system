<?php

namespace App\Http\Controllers;
use App\Models\LeadStatus;
use App\Models\Lead;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class LeadStatusController extends Controller
{
    public function statusAdd(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'status_name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 422, 'message' => $validator->errors()]);
            }

            $status = new LeadStatus();
            $status->status_name = $request->status_name;

            if ($status->save()) {

                return response()->json(['status' => 200, 'message' => 'Status Add Successfully!']);
            } else {

                return response()->json(['status' => 500, 'message' => 'Status Add Failed!']);
            }
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while status add. Please try again later.']);
        }
    }
    public function statusEdit(Request $request)
    {
        try {

            $status_data = LeadStatus::where('id', $request->status_id)->first();

            if (!$status_data) {

                return response()->json(['status' => 200, 'message' => 'Data Not Found!']);
            }

            $data[] = [

                'id' => $status_data->id,
                'status_name' => $status_data->status_name,
                'status' => $status_data->status,

            ];

            return response()->json(['status' => 200, 'message' => 'data get successfully', 'data' => $data]);
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while status edit. Please try again later.']);
        }
    }
    public function statusUpdate(Request $request)
    {
        try {
            $status_data = LeadStatus::where('id', $request->status_id)->first();

            if (!$status_data) {

                return response()->json(['status' => 200, 'message' => 'Data Not Found!']);
            }

            $validator = Validator::make($request->all(), [
                'status_name' => 'required|string|max:255',
                'status'      => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 422, 'message' => $validator->errors()]);
            }

            $status_data->status_name = $request->status_name;
            $status_data->status = $request->status;


            if ($status_data->save()) {

                return response()->json(['status' => 200, 'message' => 'Status Update Successfully!']);
            } else {

                return response()->json(['status' => 500, 'message' => 'Status Update Failed!']);
            }
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while status update. Please try again later.']);
        }
    }
    public function statusDelete(Request $request)
    {
        try {

            $status_data = LeadStatus::where('id', $request->status_id)->first();

            if (!$status_data) {

                return response()->json(['status' => 200, 'message' => 'Data Not Found!']);
            }

            if(Lead::where('lead_status', $status_data->id)->exists()){
                return response()->json(['code' => 500, 'message' => "Can't delete this status; it's assigned to lead."]);
            }

            if ($status_data->delete()) {
                return response()->json(['status' => 200, 'message' => 'Status Delete Sucessfully!']);
            }
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while status delete. Please try again later.']);
        }
    }
    public function statusList(Request $request)
    {

        try {
            $query = LeadStatus::query();

            if (isset($request->search)) {

                $query->where('status_name', 'like', '%' . $request->search . '%');
            }

            $status = $query->get();

            if ($status->isEmpty()) {

                return response()->json(['status' => 200, 'message' => 'Data Not Found!']);
            }

            $status_data = [];

            foreach ($status as $st) {

                $status_data[] = [

                    'id' => $st->id,
                    'status_name' => $st->status_name,
                    'status' => $st->status,
                    'log' => "Create DateTime : " . ($st->created_at ? $st->created_at->format('d-M-Y H:i:s') : 'N/A') .
                            " | Last Modified DateTime : " . ($st->updated_at ? $st->updated_at->format('d-M-Y H:i:s') : 'N/A'),

                ];
            }

            return response()->json(['status' => 200, 'data' => $status_data]);
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while status list. Please try again later.']);
        }
    }
    public function Status(Request $request)
    {

        try {

            $status_data = LeadStatus::where('id', $request->status_id)->first();

            if (!$status_data) {

                return response()->json(['status' => 200, 'message' => 'Data Not Found!']);
            }

            $status_data->status = $request->status;

            if ($status_data->save()) {

                return response()->json(['status' => 200, 'message' => 'Status Update Successfully!']);
            } else {

                return response()->json(['status' => 500, 'message' => 'Status Update Failed!']);
            }
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while status update. Please try again later.']);
        }
    }
    public function statusNameList(){

        try{

            $status_name_list=LeadStatus::where('status',1)->get();

            if ($status_name_list->isEmpty()) {
                return response()->json(['status' =>200,'message' => 'Data Not Found!']);
            }

            $data =[];
 
            foreach($status_name_list as $s_l){

                $data[]=[

                    'id'=>$s_l->id,
                    'status_name'=>$s_l->status_name

                ];

            }

            return response()->json(['status'=>200,'data'=>$data]);

        }catch(\Exception $e){

            return response()->json(['status' => 500, 'message' => 'An error occurred while status name list. Please try again later.']);

        }
    }
}
