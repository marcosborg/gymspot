<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RedirectToAppStore
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $userAgent = $request->header('User-Agent');

        // Verifica se é um dispositivo iOS
        if (stripos($userAgent, 'iPhone') !== false || stripos($userAgent, 'iPad') !== false) {
            return redirect('https://apps.apple.com/pt/app/gymspot/id6479336982');
        }

        // Verifica se é um dispositivo Android
        if (stripos($userAgent, 'Android') !== false) {
            return redirect('https://play.google.com/store/apps/details?id=pt.gymspot.app&hl=pt');
        }

        // Caso não seja um dispositivo mobile, prosseguir normalmente
        return $next($request);
    }
}
