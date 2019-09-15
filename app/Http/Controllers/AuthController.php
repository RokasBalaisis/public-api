<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class AuthController extends Controller
{
   // Validate the form via the LoginRequest Form Request Class
public function login(Request $request)
{
    // Attempt to log in the user with credentials
    if(Auth::guard('api')->attempt(['email' => $request->get('email'), 'password' => $request->get('password')]))
    {
        // If successful grab the user
        $customer = Auth::guard('api')->user();
        
        // If the customer has verified his account
        //if ($customer->verified)
        //{
            // Lets generate the token
            $token = Auth::guard('api')->tokenById($customer->id);
            // Return the user and the token
            return response()->json(['user_data' => Auth::guard('api')->user(), 'token' => 'Bearer ' . $token]);
         //}
         // If the user isn't verified, tell them
         //return response()->json(['error' => 'account_not_verified'], 403);
    }
    // If the credentials were incorrect, tell the user
    return response()->json(['error' => 'invalid_credentials'], 401);
}

public function reissueToken(Request $request)
{
    return response()->json(['token' => Auth::guard('api')->parseToken()->refresh()]);
}
}