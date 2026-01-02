<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        if (!auth()->check()) {
            return redirect('/login');
        }

        if (auth()->user()->role !== $role) {
            abort(403, 'Unauthorized access');
        }

        if (auth()->user()->status !== 'active') {
            auth()->logout();
            return redirect('/login')->withErrors(['email' => 'Your account has been deactivated.']);
        }

        return $next($request);
    }
}
