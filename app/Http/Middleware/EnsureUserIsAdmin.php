<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check() || ! Auth::user()->is_admin) {
            Auth::logout();

            return redirect()->route('admin.login')->withErrors([
                'email' => 'برای ورود به پنل باید با حساب مدیر وارد شوید.',
            ]);
        }

        return $next($request);
    }
}
