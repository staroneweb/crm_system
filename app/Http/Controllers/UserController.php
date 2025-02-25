<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Http\Client\Events\ResponseReceived;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\File;

class UserController extends Controller
{

    public function userAdd(Request $request)
    {


        try {

            $validator = Validator::make($request->all(), [
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:tbl_users',
                // 'email' => 'required|string|email|max:255',
                'mobile_number' => 'required|string|regex:/^\d{10}$/|unique:users',
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

            $data [] = [

                'first_name'    =>    $user_data->name,
                'last_name'     =>    $user_data->last_name,
                'email'         =>    $user_data->email,
                'mobile_number' =>    $user_data->mobile_number,
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

                'mobile_number' => [
                    'required',
                    'string',
                    'regex:/^\d{10}$/',
                    Rule::unique('users', 'mobile_number')->ignore($request->user_id),
                ],
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
            $user->mobile_number =  $request->mobile_number;

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

    public function userList(Request $request){

        try{

            $query=User::query();
            

            if(isset($request->search)){

                $query->where('name','like', '%' . $request->search . '%')
                    ->orwhere('last_name','like','%' . $request->search . '%')
                    ->orwhere('email','like','%' . $request->search . '%')
                    ->orwhere('mobile_number','like','%' . $request->search . '%')
                    ->orwhereHas('roles',function ($q) use($request){
                        $q->where('name','like','%'.$request->search.'%');
                });

            }

            $users=$query->get();

            if(!$users){

                return response()->json(['status'=>200,'message'=>'Data Not Found!']);
            }

            $user_data = [];

            foreach($users as $user){

                 $user_data[] = [

                    'id'=>$user->id,
                    'first_name' => $user->name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'mobile_number'=>$user->mobile_number,
                    'status'=> $user->status,
                    'roles' => $user->roles->pluck('name'),
                ];
            }

            return response()->json(['status'=>200,'data'=>$user_data]);

        }catch(\Exception $e){

            return response()->json(['status' => 500, 'message' => 'An error occurred while user list. Please try again later.']);

        }

    }

    public function userStatus(Request $request)
    {

        try {

            $user_id = $request->user_id;

            $user_data = User::where('id', $user_id)->first();

            if (!$user_data) {

                return response()->json(['status' => 500, 'message' => 'User Not Found!']);
            }
            $user_data->status = $request->status;

            if ($user_data->save()) {

                return response()->json(['status' => 200, 'message' => 'User Status Change Successfully!']);
            }
            
        } catch (\Exception $e) {

            return response()->json(['status' => 500, 'message' => 'An error occurred while user status change. Please try again later.']);
        }
    }
    
}
