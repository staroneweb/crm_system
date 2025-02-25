<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [

                'email' => 'required|string|max:255',
                'password' => 'required|string|min:8',
            ]);

            if ($validator->fails()) {
                Log::info('Validation errors', $validator->errors()->toArray());
                return response()->json(['status' => 500, 'message' => $validator->errors()]);
            }

            if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                return response()->json(['status' => 500, 'message' => 'Invalid Username or Password!']);
            }
            $user = Auth::user();

            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                    'status' => 200,
                    'message' => 'Login Successfully!', 
                    'access_token' => $token, 
                    'user_id' => $user->id,
                    'first_name'=>$user->name,
                    'last_name'=>$user->last_name,
                    'profile_image'=>url('profile_image' . '/' . $user->profile_image),
                    'last_login_at'=>$user->last_login_at,
                    'user_roles'=>$user->getRoleNames()
            ]);
       
        } catch (Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while user login. Please try again later.']);
        }
    }

    public function logout(Request $request)
    {
        try {
            $token = $request->bearerToken();

            $tokenInstance = \App\Models\PersonalAccessToken::findToken($token);


            if (!$tokenInstance) {

                return response()->json(['status' => 401, 'message' => 'Unauthorized']);
            }

            if ($tokenInstance->delete()) {

                $user = User::where('id', $tokenInstance->tokenable_id)->first();
                $user->last_login_at = Carbon::now();
                $user->save();

                return response()->json(['status' => 200, 'message' => 'User Logout Successfully']);
            }
        } catch (Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while user logout. Please try again later.']);
        }
    }

}
