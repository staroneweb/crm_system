<?php

namespace App\Http\Controllers;

use App\Models\Source;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SourceController extends Controller
{
    public function sourceAdd(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'source_name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 422, 'message' => $validator->errors()]);
            }

            $source = new Source();
            $source->source_name = $request->source_name;

            if ($source->save()) {

                return response()->json(['status' => 200, 'message' => 'Source Add Successfully!']);
            } else {

                return response()->json(['status' => 500, 'message' => 'Source Add Failed!']);
            }
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while source add. Please try again later.']);
        }
    }
    public function sourceEdit(Request $request)
    {
        try {

            $source_data = Source::where('id', $request->source_id)->first();

            if (!$source_data) {

                return response()->json(['status' => 200, 'message' => 'Data Not Found!']);
            }

            $data[] = [

                'id' => $source_data->id,
                'source_name' => $source_data->source_name,
                'status' => $source_data->status,

            ];

            return response()->json(['status' => 200, 'message' => 'data get successfully', 'data' => $data]);
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while source edit. Please try again later.']);
        }
    }
    public function sourceUpdate(Request $request)
    {
        try {
            $source_data = Source::where('id', $request->source_id)->first();

            if (!$source_data) {

                return response()->json(['status' => 200, 'message' => 'Data Not Found!']);
            }

            $validator = Validator::make($request->all(), [
                'source_name' => 'required|string|max:255',
                'status'      => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 422, 'message' => $validator->errors()]);
            }

            $source_data->source_name = $request->source_name;
            $source_data->status = $request->status;


            if ($source_data->save()) {

                return response()->json(['status' => 200, 'message' => 'Source Update Successfully!']);
            } else {

                return response()->json(['status' => 500, 'message' => 'Source Update Failed!']);
            }
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while source update. Please try again later.']);
        }
    }
    public function sourceDelete(Request $request)
    {
        try {

            $source_data = Source::where('id', $request->source_id)->first();

            if (!$source_data) {

                return response()->json(['status' => 200, 'message' => 'Data Not Found!']);
            }

            if(Lead::where('lead_source', $source_data->id)->exists()){
                return response()->json(['code' => 500, 'message' => "Can't delete this stage; it's assigned to lead."]);
            }

            if ($source_data->delete()) {
                return response()->json(['status' => 200, 'message' => 'Source Delete Sucessfully!']);
            }
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while source delete. Please try again later.']);
        }
    }
    public function sourceList(Request $request)
    {

        try {
            $query = Source::query();

            if (isset($request->search)) {

                $query->where('source_name', 'like', '%' . $request->search . '%');
            }

            $sources = $query->get();

            if ($sources->isEmpty()) {

                return response()->json(['status' => 200, 'message' => 'Data Not Found!']);
            }

            $source_data = [];
            foreach ($sources as $source) {

                $source_data[] = [

                    'id' => $source->id,
                    'source_name' => $source->source_name,
                    'status' => $source->status,
                    'log' => "Create DateTime : " . ($source->created_at ? $source->created_at->format('d-M-Y H:i:s') : 'N/A') .
                            " | Last Modified DateTime : " . ($source->updated_at ? $source->updated_at->format('d-M-Y H:i:s') : 'N/A'),
                ];
            }

            return response()->json(['status' => 200, 'data' => $source_data]);
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while source list. Please try again later.']);
        }
    }
    public function sourceStatus(Request $request)
    {

        try {

            $source_data = Source::where('id', $request->source_id)->first();

            if (!$source_data) {

                return response()->json(['status' => 200, 'message' => 'Data Not Foundd!']);
            }

            $source_data->status = $request->status;

            if ($source_data->save()) {

                return response()->json(['status' => 200, 'message' => 'Source Status Update Successfully!']);
            } else {

                return response()->json(['status' => 500, 'message' => 'Source Status Update Failed!']);
            }
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while source status update. Please try again later.']);
        }
    }
    public function sourceNameList(){

        try{

            $source_name_list=Source::where('status',1)->get();

            if ($source_name_list->isEmpty()) {
                return response()->json(['status' =>200,'message' => 'Data Not Found!']);
            }

            $data =[];
 
            foreach($source_name_list as $s_l){

                $data[]=[

                    'id'=>$s_l->id,
                    'source_name'=>$s_l->source_name

                ];

            }

            return response()->json(['status'=>200,'data'=>$data]);

        }catch(\Exception $e){

            return response()->json(['status' => 500, 'message' => 'An error occurred while source name list. Please try again later.']);

        }
    }
}
