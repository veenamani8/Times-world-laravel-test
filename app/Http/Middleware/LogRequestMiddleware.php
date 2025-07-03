<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LogRequestMiddleware
{
    /**
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        // Skip logging if disabled in config
        if (!config('logging.enable_request_log')) {
            return $next($request);
        }

        $startTime = microtime(true);
        $response = $next($request);
        $duration = round(microtime(true) - $startTime, 4); // round to 4 decimals

        DB::table('request_logs')->insert([
            'method'         => $request->method(),
            'url'            => $request->fullUrl(),
            'ip'             => $request->ip(),
            'response_time'  => $duration,
            'created_at'     => now(),
            'updated_at'     => now(),
        ]);

        return $response;
    }
}
