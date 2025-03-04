<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{
    //

    public function index(Request $request)
    {
        // Validate user_id parameter
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:tbl_users,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['code'=>422,'message' => $validator->errors()], 400);
        }

        // Fetch unread notifications for the given user (latest first)
        $notifications = Notification::where('user_id', $request->user_id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'code' => 1,
            'message'=>'Notification fetch successfully ',
            'data' => $notifications
        ]);
    }


    public function store(Request $request)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:tbl_users,id',
            'message' => 'required|string|max:255',
            'is_read' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // Create a new notification
        $notification = Notification::create([
            'user_id' => $request->user_id,
            'message' => $request->message,
            'is_read' => $request->is_read ?? false,
        ]);

        return response()->json(['code'=> 1,'message' => 'Notification created successfully', 'data' => $notification], 201);
    }
}
