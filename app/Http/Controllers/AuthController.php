<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\ActivityLog; 
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [

                'email' => 'required|string|email|max:255',
                'password' => 'required|string|min:8',
            ]);

            if ($validator->fails()) {
                Log::info('Validation errors', $validator->errors()->toArray());
                return response()->json(['status' => 422, 'message' => $validator->errors()]);
            }

            if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                return response()->json(['status' => 500, 'message' => 'Invalid Username or Password!']);
            }
            $user = Auth::user();

            $token = $user->createToken('auth_token')->plainTextToken;

            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity_type' => 'Login',
                'details' => json_encode(['ip' => request()->ip()]),
            ]);

            Log::info('Query Time 1: ' . microtime(true) - LARAVEL_START);
            
            return response()->json([
                    'status' => 200,
                    'message' => 'Login Successfully!', 
                    'access_token' => $token, 
                    'user_id' => $user->id,
                    'first_name'=>$user->name,
                    'last_name'=>$user->last_name,
                    'profile_image'=>$user->profile_image ? url('profile_image' . '/' . $user->profile_image) : url('profile_image' . '/'.'null.png'),
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

    public function profileShow(Request $request){

        try{

            $user_data = User::where('id',$request->user_id)->first();

            if(!$user_data){
                return response()->json(['status'=>500,'message'=>'User Not Found!']);
            }

            $initials = strtoupper(substr($user_data->name, 0, 1)) . strtoupper(substr($user_data->last_name, 0, 1));
           
            // $data=[];

            $data=[
               
                'first_name'    =>$user_data->name,
                'last_name'     =>$user_data->last_name,
                'email'         =>$user_data->email,
                'mobile_number' =>$user_data->mobile_number,
                'text'          =>$initials,
                'user_id'       =>$user_data->id,  
                'roles'         =>$user_data->getRoleNames()
            ];

            return response()->json(['status'=>200,'data'=>$data]);

        }catch(Exception $e){

            return response()->json(['status' => 500, 'message' => 'An error occurred while user profile. Please try again later.']);

        }

    }
    
    public function profileUpdate(Request $request){

        try{

            $user = User::where('id', $request->user_id)->first();

            if (!$user) {
                return response()->json(['status' => 500, 'message' => 'User Not Found!']);
            }

            $validator = Validator::make($request->all(), [

                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                // 'email' => [
                //     'required',
                //     'string',
                //     'email',
                //     'max:255',
                //     Rule::unique('tbl_users')->ignore($request->user_id)
                // ],

                'mobile_number' => [
                    'required',
                    'string',
                    'regex:/^\d{10}$/',
                    Rule::unique('tbl_users', 'mobile_number')->ignore($request->user_id),
                ],
                
            ], [
                'name.required' => 'The first name field is required.'

            ]);

            if ($validator->fails()) {

                Log::info('Validation errors', $validator->errors()->toArray());
                return response()->json(['status' => 422, 'message' =>$validator->errors()]);
            }

            $user->name = $request->first_name;
            $user->last_name = $request->last_name;
            // $user->email = $request->email;
            $user->mobile_number =  $request->mobile_number;


            if ($user->save()) {

                return response()->json(['status' => 200, 'message' => 'User update Successfully!']);
            } else {

                return response()->json(['status' => 500, 'message' => 'User update Failed!']);
            }


        }catch(Exception $e){

            return response()->json(['status' => 500, 'message' => 'An error occurred while user profile update. Please try again later.']);

        }

    }

}
