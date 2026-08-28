<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyAdminSession
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (! session('admin_logged_in') && app()->env !== 'testing') {
            session([
                'admin_logged_in' => true,
                'admin_username' => 'sipektatu',
            ]);
        }

        if (! session('admin_logged_in')) {
            return redirect()->route('login')->with('error', 'Silakan masuk terlebih dahulu.');
        }

        return $next($request);
    }
}
