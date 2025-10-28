<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|array  $roles
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect('login');
        }

        $user = Auth::user();
        
        // Nëse nuk ka role të specifikuara, lejojmë qasjen
        if (empty($roles)) {
            return $next($request);
        }
        
        // Kontrollojmë nëse përdoruesi ka ndonjë nga rolet e specifikuara
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }
        
        // Nëse përdoruesi nuk ka asnjë nga rolet e specifikuara, ridrejtojmë në faqen kryesore me mesazh gabimi
        return redirect()->route('dashboard')->with('error', 'Nuk keni leje për të qasur këtë faqe.');
    }
}
