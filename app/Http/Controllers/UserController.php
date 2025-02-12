<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{   
    public function user()
    {
        $user = User::where('id',1)->first();

       $token= $user->createToken('auth_token')->plainTextToken;

        return response()->json(['access_token' => $token]);
    }
    public function logout(Request $request){
        $token = $request->bearerToken();

        // dd($token);
        // Log::info('Received Bearer Token', ['token' => $token]);

        $tokenInstance = \App\Models\PersonalAccessToken::findToken($token);

        if (!$tokenInstance) {
            // Log::warning('Invalid Token Attempt', ['token' => $token]);
            return response()->json(['status' => 401, 'message' => 'Unauthorized'], 401);
        }
        // Log::info('Request Headers', ['headers' => $request->headers->all()]);


        $user = $tokenInstance->tokenable;
        // Log::info('User Retrieved from Token', ['user_id' => $user->id]);

        $tokenInstance->delete();
    }
}
