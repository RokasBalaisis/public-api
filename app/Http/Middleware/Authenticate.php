<?php
namespace App\Http\Middleware;


namespace App\Http\Middleware;
use Closure;
use App\User;
use Illuminate\Contracts\Auth\Factory as Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Facades\JWTAuth;

class Authenticate
{
    /**
     * The authentication guard factory instance.
     *
     * @var \Illuminate\Contracts\Auth\Factory
     */
    protected $auth;
    /**
     * Create a new middleware instance.
     *
     * @param  \Illuminate\Contracts\Auth\Factory  $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $payload = $this->auth->guard($guard)->manager()->getJWTProvider()->decode(\JWTAuth::getToken()->get());
        dd($payload);
        if(!$this->auth->guard($guard)->check())
        {
            try
            {
                $refreshed_token = $this->auth->guard($guard)->refresh();
                $payload = \JWTAuth::manager()->getJWTProvider()->decode($refreshed_token);
                $currentuser = User::find($payload['sub']);
                $credentials = ["email" => $currentuser->email, "password" => $currentuser->password];
                $this->auth->guard($guard)->attempt($credentials);
                return $next($request)->header("Authorization", "Bearer " . $refreshed_token);
            }
            catch (JWTException $e)
            {
                if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                    return response("Token is Invalid.", 401);
                }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                    return response('Token is Expired.', 401);
                }
            }   
            return response('Unauthorized.', 401);
        }

        return $next($request);
    }
    
}


// catch (JWTException $e) {
//     if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
//         return response("Token is Invalid.");
//     }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
//         return response('Token is Expired.');
//     }else{
//         return response('Unauthorized.', 401);
//     }