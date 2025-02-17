<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CustomRoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $user = Auth::guard('sanctum')->user();

        // Check if user has the required role
        // if (!$user->hasAnyRole($role)) {
        //     return response()->json(['status'=>403,'message' => 'Access denied. You need the "' . $role . '" role to proceed.']);
        // }

        $userRoles = $user->getRoleNames()->toArray();
        // dd($userRoles);
        // $userPermissions = $user->getPermissionsViaRoles()->pluck('name')->toArray(); // Example: ['user.add', 'user.edit']

       
        if (!$user->hasAnyRole($userRoles)) {
            return response()->json(['status'=>403,'message' => 'Access denied']);
        }
        return $next($request);
    }
}
