<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class LogActivity
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        if (Auth::check()) {
            $user = Auth::user();
            $method = $request->method();

            if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'])) {
                ActivityLog::create([
                    'user_id'    => $user->id,
                    'log_type'   => $method === 'DELETE' ? 'danger' : 'info',
                    'action'     => $method,
                    'module'     => $request->segment(2) ?? 'general',
                    'description' => $user->name . ' melakukan ' . $method . ' pada ' . $request->path(),
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'url'        => $request->fullUrl(),
                    'method'     => $method,
                ]);
            }
        }

        return $response;
    }
}
