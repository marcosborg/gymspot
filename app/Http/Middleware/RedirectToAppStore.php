<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RedirectToAppStore
{
    public function handle(Request $request, Closure $next)
    {
        $userAgent = $request->header('User-Agent');

        // Verifica se é um dispositivo iOS
        if (stripos($userAgent, 'iPhone') !== false || stripos($userAgent, 'iPad') !== false) {
            return redirect()->away('itms-apps://apps.apple.com/pt/app/gymspot/id6479336982');
        }

        // Verifica se é um dispositivo Android
        if (stripos($userAgent, 'Android') !== false) {
            return redirect()->away('market://details?id=com.gymspot.app');
        }

        // Caso não seja um dispositivo mobile, prosseguir normalmente
        return $next($request);
    }
}
