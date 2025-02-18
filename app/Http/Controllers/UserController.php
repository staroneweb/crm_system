<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;

class UserController extends Controller
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

            return response()->json(['status' => 200, 'message' => 'Login Successfully!', 'access_token' => $token, 'user_id' => $user->id]);
        } catch (Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while user login. Please try again later.']);
        }
    }

    public function userAdd(Request $request)
    {


        try {

            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:tbl_users',
                // 'email' => 'required|string|email|max:255',
                // 'mobile_number' => 'required|string|regex:/^\d{10}$/|unique:users',
                'profile_image' => 'nullable|mimes:jpeg,png,jpg',
                'password' => 'required|string|min:8',
                'role' => 'required'
            ], [
                'name.required' => 'The first name field is required.'
            ]);

            if ($validator->fails()) {
                Log::info('Validation errors', $validator->errors()->toArray());
                return response()->json(['status' => 500, 'message' => $validator->errors()]);
            }

            $user = new User();

            if (request()->hasFile('profile_image')) {
                $file = request()->file('profile_image');
                $filename = $file->getClientOriginalName();
                $ext = $file->getClientOriginalExtension();
                $newfilename = md5($filename) . rand(10, 1000) . time() . '.' . $ext;

                $user->profile_image = $newfilename ?? null;
            }

            // $user->role_id = $request->role_id;
            $user->name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;
            $user->status = 1;  // default active
            $user->password = Hash::make($request->password);

            if ($user->save()) {

                $user->assignRole($request->role);

                if ($request->hasFile('profile_image')) {
                    $request->file('profile_image')->move(public_path('profile_image'), $newfilename);
                }

                return response()->json(['status' => 200, 'message' => 'User Add Successfully!']);
            } else {

                return response()->json(['status' => 500, 'message' => 'User Add Failed!']);
            }
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while user add. Please try again later.']);
        }
    }

    public function userEdit(Request $request)
    {

        try {

            $user_id = $request->user_id;

            $user_data = User::where('id', $user_id)->first();

            if (!$user_data) {
                return response()->json(['status' => 500, 'message' => 'User Not Found!']);
            }

            $data = [];

            $data[] = [

                'first_name'    =>    $user_data->name,
                'last_name'     =>    $user_data->last_name,
                'email'         =>    $user_data->email,
                'profile_image' =>    url('profile_image' . '/' . $user_data->profile_image),
                'role'          =>    $user_data->getRoleNames()
            ];

            return response()->json(['status' => 200, 'message' => 'User data fetch successfully!', 'data' => $data]);
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while user edit. Please try again later.']);
        }
    }
    public function userUpdate(Request $request)
    {

        try {

            $user = User::where('id', $request->user_id)->first();

            if (!$user) {
                return response()->json(['status' => 500, 'message' => 'User Not Found!']);
            }

            $validator = Validator::make($request->all(), [

                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => [
                    'required',
                    'string',
                    'email',
                    'max:255',
                    Rule::unique('tbl_users')->ignore($request->user_id)
                ],
                // 'email' => 'required|string|email|max:255',

                // 'mobile_number' => 'required|string|regex:/^\d{10}$/|unique:users',
                'profile_image' => 'nullable|mimes:jpeg,png,jpg',
                'password' => 'required|string|min:8',
                // 'role' => 'required'
            ], [
                'name.required' => 'The first name field is required.'

            ]);

            if ($validator->fails()) {

                Log::info('Validation errors', $validator->errors()->toArray());
                return response()->json(['status' => 500, 'message' => $validator->errors()]);
            }

            

            if (request()->hasFile('profile_image')) {

                $image_path = public_path('profile_image/' . $user->profile_image);

                if (File::exists($image_path)) {

                    unlink($image_path);
                }


                $file = request()->file('profile_image');
                $filename = $file->getClientOriginalName();
                $ext = $file->getClientOriginalExtension();
                $newfilename = md5($filename) . rand(10, 1000) . time() . '.' . $ext;

                $user->profile_image = $newfilename;
            }

            $user->name = $request->first_name;
            $user->last_name = $request->last_name;
            $user->email = $request->email;

            if ($user->save()) {

                $user->syncRoles($request->role);

                if (request()->hasFile('profile_image')) {
                    $request->profile_image->move(public_path('profile_image'), $newfilename);
                }

                return response()->json(['status' => 200, 'message' => 'User Update Successfully!']);
            }
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while user update. Please try again later.']);
        }
    }

    public function userDelete(Request $request)
    {  

        try {

            $user = User::find($request->user_id);

            if (!$user) {

                return response()->json(['status' => 500, 'message' => 'User Not Found!']);
            }

            $image_path = public_path('profile_image/' . $user->profile_image);

            if (File::exists($image_path)) {
                unlink($image_path);
            }

            if ($user->delete()) {
                return response()->json(['status' => 200, 'message' => 'User Delete Sucessfully!']);
            }

        } catch (Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while user delete. Please try again later.']);
        }
    }
    
    public function userStatus(){
        
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


    public function test()
    {
        dd("test");
    }
}
