<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Setting;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        // Allow super admins to bypass maintenance mode
        if (auth()->check() && auth()->user()->role === 'super_admin') {
            return $next($request);
        }

        // Check if maintenance mode is enabled
        if (Setting::isMaintenanceMode()) {
            return response()->view('maintenance', [
                'message' => Setting::get('maintenance_message', 'We are currently performing scheduled maintenance.')
            ], 503);
        }

        return $next($request);
    }
}
