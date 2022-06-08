<?php

namespace App\Http\Middleware;

class JsonEverywhere
{
    public function handle(\Illuminate\Http\Request $request, \Closure $next)
    {
        $request->headers->set('Accept', 'application/json' );
        $request->headers->set('Content-Type', 'application/json' );

        return $next($request);
    }
}
