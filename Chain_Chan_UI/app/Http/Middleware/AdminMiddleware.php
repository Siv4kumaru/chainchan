<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
public function handle(Request $request, Closure $next): Response
{


if (Auth::check() && Auth::user()->role === 'admin') {
    return $next($request);
}

// Redirect non-admins to welcome with error message
return redirect('/')->with('error_message', 'ACCESS_DENIED: Administrator privileges required.');
}

}