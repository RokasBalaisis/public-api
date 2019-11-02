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
        $validator = Validator::make($request->all(), [
            'username' => ['required', 'string', 'max:50', 'unique:users', 'regex:/(^([a-zA-Z]+)(\d+)?$)/u'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:6', 'alpha_dash'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
            DB::table('users')->insert(['username' => $request->username, 'email' => $request->email, 'password' => app('hash')->make($request->password), 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
            $user = DB::table('users')->where('username', $request->username)->where('email', $request->email)->first();
            DB::table('user_role')->insert(['user_id' => $user->id, 'role_id' => 2]);

            //return successful response
            return response()->json(['user' => $user, 'message' => 'User registration was successful'], 201);
        
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
        if (! $token = Auth::attempt($credentials)) {
            return response()->json(['message' => ['Invalid credentials']], 422);
        }
        $payload = Auth::payload();
        DB::table('users')->where('email', $request->email)->update(['status' => 1, 'exp' => $payload['exp']]);
        return response()->json(["message" => "User successfully logged in"], 200)->header('Authorization', 'Bearer ' . $token);
    }


    public function logout()
    {
        Auth::logout();
        return response()->json(["message" => "Logged out"], 200);
    }
    
}