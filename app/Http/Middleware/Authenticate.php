<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
 /**
 * Handle an incoming request.
 *
 * @param \Illuminate\Http\Request $request
 * @param \Closure $next
 * @param string|null $guard
 * @return mixed
 */
 public function handle($request, Closure $next, $guard = null)
 {
  // BEFORE THE REQUEST ENTERS OUR OUR PROJECT
  // Check if the users is logged 
  // This returns true if the user is not logged
  if (Auth::guard($guard)->guest()) {
   // If the request is AJAX (if it is coming from AngularJS)
   if ($request->ajax() || $request->wantsJson()) {
    // Return 401 / Unauthorised
    return response('Unauthorised.', 401);
   } else {
    // If the request was not from AJAX, redirect to login page.
    return redirect()->guest('login');
   }
  }
  // If the user was logged in, we can process the request
  $response = $next($request);
  
  // AFTER THE REQUEST HAS BEEN THROUGH OUR PROJECT
  // If the user is logged in, and the method refresh exists with
  // this auth package (i.e. we're using JWT), then we want to
  // refresh the token for the user's next request.
  if (Auth::guard($guard)->check() && method_exists(Auth::guard($guard), 'refresh'))
  {
    // Refresh the token, and place it in the headers for the user to pick up at the front end.
    $response->headers->set('Authorization', 'Bearer ' . Auth::guard($guard)->refresh());
  }
  return $response;
 }
}