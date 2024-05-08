<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\Middleware as ControllersMiddleware;
use Illuminate\Session\Middleware\Middleware;
use Illuminate\Session\SessionManager;
use Symfony\Component\HttpFoundation\Response;

class CustomSessionMiddleware 
{

    // public function handle(Request $request, Closure $next): Response
    // {
    //     return $next($request);

    // }
    // public function getSession($request){
    //     $session = $request->getSession();
    //     return $session;
    // }
    // public function __construct(SessionManager $sessionManager)
    // {
    //     $this->$sessionManager = $sessionManager;
    // }
}
