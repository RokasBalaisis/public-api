<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Input;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

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
        $customNames = array(
            'registration_email' => 'email',
            'registration_password' => 'password'
         );

        //validate incoming request 
        $validator = Validator::make(Input::all(), [
            'username' => ['required', 'string', 'max:50', 'unique:users', 'regex:/(^([a-zA-Z]+)(\d+)?$)/u'],
            'registration_email' => ['exists:users,email','required', 'email', 'unique:users,email'],
            'registration_password' => ['exists:users,password','required', 'min:6', 'regex:/^.*(?=.{3,})(?=.*[a-zA-Z])(?=.*[0-9])(?=.*[\d\X])(?=.*[!$#%]).*$/'],
        ]);

        $validator->setAttributeNames($customNames);

        // if(DB::table('users')->where('email', $request->input('registration_email'))->count() > 0)
        // {
        //     return response()->json(['message' => ['User with this email already exists!']], 409);
        // }
        if ($validator->fails()) {
            return response()->json($validator->errors(), 401);
        }
        try {
            DB::table('users')->insert(['username' => $request->username, 'email' => pluck($request->registration_email), 'password' => app('hash')->make($request->registration_password)]);
            $user = DB::table('users')->where('username', $request->username)->where('email', $request->registration_email)->first();
            DB::table('user_role')->insert(['user_id' => $user->id, 'role_id' => 2]);

            //return successful response
            return response()->json(['user' => $user, 'message' => 'User registration was successful'], 201);

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
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only(['email', 'password']);
        if(DB::table('users')->where('email', $request->email)->count() > 0)
        {
            $result = DB::table('users')->where('email', $request->email)->first();
            if(DB::table('users')->where('id', $result->id)->first()->status == 1 && $result->exp > Carbon::now()->timestamp)
            {
                return response()->json(['error' => ['User is already logged in']], 401);
            }
        }
        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['message' => ['Invalid credentials']], 401);
        }
        $payload = Auth::payload();
        DB::table('users')->where('email', $request->email)->update(['status' => 1, 'jti' => $payload['jti'], 'exp' => $payload['exp']]);
        return response()->json(["message" => "User successfully logged in"], 200)->header('Authorization', 'Bearer ' . $token);
    }


    public function logout(Request $request)
    {
        DB::table('users')->where('id', Auth::user()->getAuthIdentifier())->update(['status' => 0]);
        Auth::logout();

        return response()->json(["message" => "Logged out"], 200);
    }
    
}