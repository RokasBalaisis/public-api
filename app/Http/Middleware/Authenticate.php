<?php
namespace App\Http\Middleware;


namespace App\Http\Middleware;
use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

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
        
        if(!$this->auth->guard($guard)->check())
        {
            try
            {
                $refreshed_token = $this->auth->guard($guard)->refresh();
                return $next($request)->header("Authorization", "Bearer " . $refreshed_token);
            }
            catch (JWTException $e)
            {
                if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException){
                    return response("Token is Invalid.");
                }else if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException){
                    return response('Token is Expired.');
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