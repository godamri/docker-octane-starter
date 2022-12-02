<?php

namespace App\Http\Middleware;

class JsonEverywhere
{
    public function handle(\Illuminate\Http\Request $request, \Closure $next)
    {
        $request->headers->set('Accept', 'application/json' );
        $request->headers->set('Content-Type', 'application/json' );
        $request->headers->set('X-Request-Id', 'WT.' . strtoupper(sha1(time())) );

        $response = $next($request);

        $response->headers->set('X-Request-Id', $request->header('X-Request-Id') );

        return $response;
    }
}
