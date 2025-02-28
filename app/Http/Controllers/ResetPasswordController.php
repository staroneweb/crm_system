<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\PasswordReset;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;


class ResetPasswordController extends Controller
{
    public function reset(Request $request)
    {

        try {
            $validator = Validator::make($request->all(), [

                'token' => 'required',
                'password' => 'required|string|min:8',
                'password_confirmation' => 'required|string|min:8|same:password'

            ], [
                'password_confirmation.same' => 'The password and confirm password must be the same.',
                'password_confirmation.required' => 'The confirm password field is required.',
                'password.min' => 'The password must be at least 8 characters.',
                'password_confirmation.min' => 'The conform password must be at least 8 characters.'

            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 500, 'message' => $validator->errors()]);
            }

            $password_reset = PasswordReset::where('token', $request->token)->first();

            if (!$password_reset) {
                return response()->json(['status' => 500, 'message' => 'Invalid token!']);
            }

            $now = Carbon::now()->format('Y-m-d H:i:s');
            $expiry = Carbon::parse($password_reset->expires_at)->format('Y-m-d H:i:s');

            if (strtotime($now) > strtotime($expiry)) {

                return response()->json(['status' => 500, 'message' => 'Your reset password link has expired. Please request a new one.']);
            }

            $user_data = User::where('email', $password_reset->email)->first();

            if (!$user_data) {
                return response()->json(['status' => 500, 'message' => 'User not found!']);
            }

            $user_data->password = Hash::make($request->password);

            if($user_data->save()){

                $password_reset->delete();
                return response()->json(['status'=>200,'message'=>'Password change successfully!']);

            }else{

                return response()->json(['status'=>200,'message'=>'Password change failed!']);

            }

        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while reset password. Please try again later.']);
        }
    }
}
