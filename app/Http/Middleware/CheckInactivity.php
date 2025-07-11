<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Carbon\Carbon;

class CheckInactivity
{
    public function handle($request, Closure $next)
    {
        if ($request->is('login') && Auth::check()) {
            $this->forceLogout($request);
            return redirect()->route('login')
                ->with('message', 'Anda telah logout untuk keamanan');
        }

        if (Auth::check()) {
            $lastActivity = Session::get('last_activity');
            $inactivityLimit = Carbon::now()->subMinutes(30);

            if ($lastActivity && Carbon::parse($lastActivity) < $inactivityLimit) {
                $this->forceLogout($request);
                return redirect()->route('login')
                    ->with('message', 'Sesi telah berakhir karena tidak aktif selama 30 menit');
            }

            Session::put('last_activity', Carbon::now());
        }

        return $next($request);
    }

    protected function forceLogout($request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        Session::forget('last_activity');
    }
}
