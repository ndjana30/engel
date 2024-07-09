<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin

{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        
        if ($request->user() && $request->user()->rule_id!=1) {
            // dd($request->user()->rule_id);
            abort(403, 'Unauthorized action this is for admin unly.');
        }
        return $next($request);
    }
}
