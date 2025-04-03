<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Exception;
use Illuminate\Support\Facades\Validator;


class MeetingController extends Controller
{
    
    public function index(Request $request)
    {
        try {
            $meetings = Meeting::with('lead')->get()->map(function ($meeting) {
                return [
                    'id' => $meeting->id,
                    'lead_name' => $meeting->lead->name ?? null, // Ensure lead exists before accessing 'name'
                    'meeting_date' => $meeting->meeting_date,
                    'location' => $meeting->location,
                    'agenda' => $meeting->agenda,
                    'created_at' => $meeting->created_at->format('Y-m-d H:i:s'), // Format datetime
                    'updated_at' => $meeting->updated_at->format('Y-m-d H:i:s'),
                ];
            });
    
            return response()->json([
                'code' => 200,
                'message' => 'Meeting data fetch successful',
                'data' => $meetings
            ], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json([
                'code' => 500,
                'message' => 'Meeting data fetch error',
                'data' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    

    
    public function store(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                'lead_id' => 'required|exists:tbl_leads,id',
                'meeting_date' => 'required|date',
                'location' => 'nullable|string|max:255',
                'agenda' => 'nullable|string',
            ]);


            if ($validator->fails()) {
                return response()->json(['status' => 422, 'message' => $validator->errors()]);
            }




            $meeting = Meeting::create($request->all());

            return response()->json(['code'=>200, 'message' => 'Meeting created successfully', 'data' => $meeting], Response::HTTP_CREATED);
        } catch (Exception $e) {
            return response()->json(['code'=>500, 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    
    public function show(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                'id' => 'required|integer|exists:tbl_meetings,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 422, 'message' => $validator->errors()]);
            }

            $meeting = Meeting::with('lead')->find($request->id);

            return response()->json(['code'=>200,'message' => 'Meeting fetch successfully', 'data' => $meeting], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['code'=>500,'message' => 'Meeting fetch successfully', 'data' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    
    public function update(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                'id' => 'required|integer|exists:tbl_meetings,id',
                'lead_id' => 'required|exists:tbl_leads,id',
                'meeting_date' => 'required|date',
                'location' => 'nullable|string|max:255',
                'agenda' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 422, 'message' => $validator->errors()]);
            }



            $meeting = Meeting::find($request->id);
            $meeting->update($request->all());

            return response()->json(['status' => 200, 'message' => 'Meeting updated successfully', 'data' => $meeting], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['status' => 500,'message' => 'Meeting updated error', 'data' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

   
    public function destroy(Request $request)
    {
        try {
            $validator = Validator::make(
                $request->all(),
                [
                'id' => 'required|integer|exists:tbl_meetings,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 422, 'message' => $validator->errors()]);
            }

            $meeting = Meeting::find($request->id);
            $meeting->delete();

            return response()->json(['status' => 200, 'message' => 'Meeting deleted successfully'], Response::HTTP_OK);
        } catch (Exception $e) {
            return response()->json(['status' => 500, 'message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
