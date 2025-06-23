<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Throwable;

class PingDatabase
{
    public function handle($request, Closure $next)
    {
        $maxAttempts = 5;
        $attempt = 0;

        while ($attempt < $maxAttempts) {
            try {
                DB::connection()->getPdo();
                break;
            } catch (Throwable $e) {
                $attempt++;
                sleep(2); // Chờ DB khởi động lại
            }
        }

        return $next($request);
    }
}
