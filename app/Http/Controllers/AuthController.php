<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
   /**
     * Store a new user.
     *
     * @param  Request  $request
     * @return Response
     */
    public function register(Request $request)
    {
        //validate incoming request 
        $this->validate($request, [
            'name' => 'required|string',
            'registration_email' => 'required|email',
            'registration_password' => 'required',
        ]);

        if(DB::table('users')->where('email', $request->input('registration_email'))->count() > 0)
        {
            return response()->json(['message' => 'User with this email already exists!'], 409);
        }

        try {
            dd("lol");
            $user = new User;
            $user->name = $request->input('name');
            $user->email = $request->input('registration_email');
            $plainPassword = $request->input('registration_password');
            $user->password = app('hash')->make($plainPassword);

            $user->save();

            //return successful response
            return response()->json(['user' => $user, 'message' => 'CREATED'], 201);

        } catch (\Exception $e) {
            //return error message
            return response()->json(['message' => 'User Registration Failed!'], 409);
        }

    }

       /**
     * Get a JWT via given credentials.
     *
     * @param  Request  $request
     * @return Response
     */
    public function login(Request $request)
    {
          //validate incoming request 
        $this->validate($request, [
            'email' => 'required|string',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);

        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return response()->json("User successfully logged in", 200)->header('Authorization', 'Bearer ' . $token);
    }

    
}