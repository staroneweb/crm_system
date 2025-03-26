<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use Illuminate\Support\Facades\Validator;
use Exception;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        try {
            // Validate user_id parameter
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:tbl_users,id'
            ]);

            if ($validator->fails()) {
                return response()->json(['code' => 422, 'message' => $validator->errors()], 400);
            }

            // Fetch notifications
            $notifications = Notification::where('user_id', $request->user_id)
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'code' => 1,
                'message' => 'Notifications fetched successfully',
                'data' => $notifications
            ]);
        } catch (Exception $e) {
            return response()->json(['code' => 0, 'message' => 'Something went wrong', 'error' => $e->getMessage()], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validate request
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:tbl_users,id',
                'message' => 'required|string|max:255',
                'is_read' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            // Create notification
            $notification = Notification::create([
                'user_id' => $request->user_id,
                'message' => $request->message,
                'is_read' => $request->is_read ?? false,
            ]);

            return response()->json(['code' => 1, 'message' => 'Notification created successfully', 'data' => $notification], 201);
        } catch (Exception $e) {
            return response()->json(['code' => 0, 'message' => 'Failed to create notification', 'error' => $e->getMessage()], 500);
        }
    }

    public function show(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:notifications,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $notification = Notification::find($request->id);

            if (!$notification) {
                return response()->json(['code' => 0, 'message' => 'Notification not found'], 404);
            }

            return response()->json(['code' => 1, 'message' => 'Notification retrieved successfully', 'data' => $notification], 200);
        } catch (Exception $e) {
            return response()->json(['code' => 0, 'message' => 'Something went wrong', 'error' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:notifications,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $notification = Notification::find($request->id);

            if (!$notification) {
                return response()->json(['code' => 0, 'message' => 'Notification not found'], 404);
            }

            // Validate other fields
            $validator = Validator::make($request->all(), [
                'user_id' => 'exists:tbl_users,id',
                'message' => 'string|max:255',
                'is_read' => 'boolean'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            // Update notification
            $notification->update($request->only(['user_id', 'message', 'is_read']));

            return response()->json(['code' => 1, 'message' => 'Notification updated successfully', 'data' => $notification], 200);
        } catch (Exception $e) {
            return response()->json(['code' => 0, 'message' => 'Failed to update notification', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|exists:notifications,id',
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            $notification = Notification::find($request->id);

            if (!$notification) {
                return response()->json(['code' => 0, 'message' => 'Notification not found'], 404);
            }

            $notification->delete();

            return response()->json(['code' => 1, 'message' => 'Notification deleted successfully'], 200);
        } catch (Exception $e) {
            return response()->json(['code' => 0, 'message' => 'Failed to delete notification', 'error' => $e->getMessage()], 500);
        }
    }
}
