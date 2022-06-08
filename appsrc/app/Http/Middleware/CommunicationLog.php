<?php

namespace App\Http\Middleware;

use App\Utils\CommunicationLogService;
use Closure;
use Illuminate\Http\Request;

class CommunicationLog
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        app(CommunicationLogService::class)->setStartTime(microtime(true));
        app(CommunicationLogService::class)->initialize($request);

        $response = $next($request);

        app(CommunicationLogService::class)->commit(
            [
                'header' => $response->headers->all(),
                'content' => $response->getContent(),
            ]
        );

        return $response;
    }
}
