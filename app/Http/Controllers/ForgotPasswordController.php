<?php

namespace App\Http\Controllers;

use App\Models\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;


class ForgotPasswordController extends Controller
{
    public function sendResetLinkEmail(Request $request)
    {

        try{

            $validator = Validator::make($request->all(), [
                'email' => 'required|email|exists:tbl_users,email'
            ], [
                'email.exists' => 'The provided email does not exist in our records.'
            ]);

            if ($validator->fails()) {
                return response()->json(['status' => 500, 'message' => $validator->errors()]);
            }

            $token =Str::random(60);
            $expires_at = Carbon::now()->addMinutes(5);

            PasswordReset::updateOrCreate(
                ['email' => $request->email], 
                [
                    'token' => $token,
                    'expires_at' => $expires_at
                ]
            );
                    
            $token = PasswordReset::where('email', $request->email)->value('token');

            $frontendLink = "http://localhost:3000/reset-password?token=$token";

            Mail::send('emails.password_reset', ['frontendLink' => $frontendLink], function ($message) use ($request) {
                $message->from('no-reply@yourdomain.com', 'Your App Name')
                    ->to($request->email)
                    ->subject('Reset Your Password');
            });

            return response()->json(['status'=>200,'message'=>'Mail sent successfully!']);
       }
       catch(\Exception $e){

         return response()->json(['status' => 500, 'message' => 'An error occurred while email sent. Please try again later.']);

       }
    }
}
