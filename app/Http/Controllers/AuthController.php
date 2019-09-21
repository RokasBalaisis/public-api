<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
   // Validate the form via the LoginRequest Form Request Class
   public function login(Request $request) {

    // Validate
    $this->userValidator->validateLogin($request);

    // Attempt login
    $credentials = $request->only("email", "password");

    if (!$token = Auth::attempt($credentials)) {
        throw ValidationException::withMessages(["login" => "Incorrect email or password."]);
    }

    return [
        "token" => [
            "access_token" => $token,
            "token_type"   => "Bearer",
            "expire"       => (int) Auth::guard()->factory()->getTTL()
        ]
    ];
}

public function reissueToken(Request $request)
{
    $tokenExpired = false;
    try{
       Auth::guard('api')->parseToken();
    }
    catch (JWTException $e)
    {
        $tokenExpired = true;
    }
    
    if(!$tokenExpired)
    {
        return response()->json(['token' => Auth::guard('api')->parseToken()->refresh()]);
    }
    else
    {
        return response()->json(['Token is expired and cannot be refreshed anymore'], 401);
    }
}
}