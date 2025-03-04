<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Sales;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;


class SalesController extends Controller
{
    public function salesAdd(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'stage'         => 'required',
                'probability'   => 'required',
                'forecast_date' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 422, 'message' => $validator->errors()]);
            }

            $forecast_date = Carbon::createFromFormat('d-m-Y', $request->forecast_date)->format('Y-m-d');

            $sales = new Sales();
            $sales->lead_id = $request->lead_id;
            $sales->stage = $request->stage;
            $sales->probability = $request->probability;
            $sales->forecast_date = $forecast_date;

            if ($sales->save()) {

                return response()->json(['status' => 200, 'message' => 'Sales save successfully!']);
            } else {

                return response()->json(['status' => 200, 'message' => 'Sales save failed!']);
            }

        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while sale add. Please try again later.']);
        }
    }
    public function salesEdit(Request $request)
    {
        try {
            
            $sales_data=Sales::where('id',$request->sales_id)->first();
            
            if(!$sales_data){

                return response()->json(['status'=>500,'message'=>'Sales not found!']);
            }

            $data[]=[

                'sales_id'=>$sales_data->id,
                'lead_id'=>$sales_data->lead_id,
                'stage' =>$sales_data->stage,
                'probability'=>$sales_data->probability,
                'forecast_date'=>Carbon::parse($sales_data->forecast_date)->format('d-m-Y')
            ];
    
            return response()->json(['status'=>200,'data'=>$data]);

        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while sale edit. Please try again later.']);

        }
    }
    public function salesUpdate(Request $request)
    {
        try {

            $sales_data=Sales::where('id',$request->sales_id)->first();
            
            if(!$sales_data){

                return response()->json(['status'=>500,'message'=>'Sales not found!']);
            }

            $validator = Validator::make($request->all(), [
                'stage'         => 'required',
                'probability'   => 'required',
                'forecast_date' => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 422, 'message' => $validator->errors()]);
                
            }

            $forecast_date = Carbon::createFromFormat('d-m-Y', $request->forecast_date)->format('Y-m-d');

            $sales_data->lead_id = $request->lead_id;
            $sales_data->stage = $request->stage;
            $sales_data->probability = $request->probability;
            $sales_data->forecast_date = $forecast_date;

            if ($sales_data->save()) {

                return response()->json(['status' => 200, 'message' => 'Sales update successfully!']);
            } else {

                return response()->json(['status' => 200, 'message' => 'Sales update failed!']);
            }

        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while sale update. Please try again later.']);
        }
    }
    public function salesDelete(Request $request)
    {
        try {

            $sales_data=Sales::where('id',$request->sales_id)->first();
            
            if(!$sales_data){

                return response()->json(['status'=>500,'message'=>'Sales not found!']);
            }

            if ($sales_data->delete()) {

                return response()->json(['status' => 200, 'message' => 'Sales delete successfully!']);
            } else {

                return response()->json(['status' => 200, 'message' => 'Sales delete failed!']);
            }

        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while sale delete. Please try again later.']);
        }
    }
    public function salesList(Request $request)
    {
        try {

            $query= Sales::with(['leads']);

            if (preg_match('/\d{2}-\d{2}-\d{4}/', $request->search)) {

                $forecast_date = Carbon::createFromFormat('d-m-Y', $request->search)->format('Y-m-d');

            } else {

                $forecast_date = null;
            }

            if (isset($request->search)) {

                $query->where('stage', 'like', '%' . $request->search . '%')
                    ->orwhere('probability', 'like', '%' . $request->search . '%')
                    ->orwhere('forecast_date', $forecast_date)
                    ->orwhereHas('leads', function ($q) use ($request) {
                        $q->where('source', 'like', '%' . $request->search . '%');
                    });
            }

            $sale_data = $query->get();

            if (!$sale_data) {

                return response()->json(['status' => 200, 'message' => 'Data Not Found!']);
            }

            $data =[];

            foreach($sale_data as $s_d){

                $data[]=[

                   'stage'=>$s_d->stage,
                   'probability'=>$s_d->probability,
                   'forecast_date'=>Carbon::parse($s_d->forecast_date)->format('d-m-Y'),
                   'lead_source'=>$s_d->leads->source
                ];

            }

            return response()->json(['status' => 200, 'data' => $data]);

        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while task list. Please try again later.']);
        }
    }
}
