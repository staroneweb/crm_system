<?php

namespace App\Http\Controllers;
use App\Models\Stage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class StageController extends Controller
{
    public function stageAdd(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'stage_name' => 'required|string|max:255|regex:/^[A-Za-z\s]+$/',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 422, 'message' => $validator->errors()]);
            }

            $stage = new Stage();
            $stage->stage_name = $request->stage_name;

            if ($stage->save()) {

                return response()->json(['status' => 200, 'message' => 'Stage Add Successfully!']);
            } else {

                return response()->json(['status' => 500, 'message' => 'Stage Add Failed!']);
            }
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while stage add. Please try again later.']);
        }
    }
    public function stageEdit(Request $request)
    {
        try {

            $stage_data = Stage::where('id', $request->stage_id)->first();

            if (!$stage_data) {

                return response()->json(['status' => 200, 'message' => 'Data Not Found!']);
            }

            $data[] = [

                'id' => $stage_data->id,
                'stage_name' => $stage_data->stage_name,
                'status' => $stage_data->status,

            ];

            return response()->json(['status' => 200, 'message' => 'data get successfully', 'data' => $data]);
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while stage edit. Please try again later.']);
        }
    }
    public function stageUpdate(Request $request)
    {
        try {
            $stage_data = Stage::where('id', $request->stage_id)->first();

            if (!$stage_data) {

                return response()->json(['status' => 200, 'message' => 'Data Not Found!']);
            }

            $validator = Validator::make($request->all(), [
                'stage_name' => 'required|string|max:255|regex:/^[A-Za-z\s]+$/',
                'status'      => 'required'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 422, 'message' => $validator->errors()]);
            }

            $stage_data->stage_name = $request->stage_name;
            $stage_data->status = $request->status;


            if ($stage_data->save()) {

                return response()->json(['status' => 200, 'message' => 'Stage Update Successfully!']);
            } else {

                return response()->json(['status' => 500, 'message' => 'Stage Update Failed!']);
            }
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while stage update. Please try again later.']);
        }
    }
    public function stageDelete(Request $request)
    {
        try {

            $stage_data = Stage::where('id', $request->stage_id)->first();

            if (!$stage_data) {

                return response()->json(['status' => 200, 'message' => 'Data Not Found!']);
            }

            if ($stage_data->delete()) {
                return response()->json(['status' => 200, 'message' => 'Stage Delete Sucessfully!']);
            }
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while stage delete. Please try again later.']);
        }
    }
    public function stageList(Request $request)
    {

        try {
            $query = Stage::query();

            if (isset($request->search)) {

                $query->where('stage_name', 'like', '%' . $request->search . '%');
            }

            $stages = $query->get();

            if ($stages->isEmpty()) {

                return response()->json(['status' => 200, 'message' => 'Data Not Found!']);
            }

            $stage_data = [];

            foreach ($stages as $stage) {

                $stage_data[] = [

                    'id' => $stage->id,
                    'stage_name' => $stage->stage_name,
                    'status' => $stage->status
                ];
            }

            return response()->json(['status' => 200, 'data' => $stage_data]);
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while stage list. Please try again later.']);
        }
    }
    public function stageStatus(Request $request)
    {

        try {

            $stage_data = Stage::where('id', $request->stage_id)->first();

            if (!$stage_data) {

                return response()->json(['status' => 200, 'message' => 'Data Not Found!']);
            }

            $stage_data->status = $request->status;

            if ($stage_data->save()) {

                return response()->json(['status' => 200, 'message' => 'Stage Status Update Successfully!']);
            } else {

                return response()->json(['status' => 500, 'message' => 'Stage Status Update Failed!']);
            }
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while stage status update. Please try again later.']);
        }
    }

}
