<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class AcceptLanguage
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $lang = $request->lang ?? 'en';

        if (in_array($lang, ['en', 'id'])) {
            App::setLocale($lang);
        } else {
            App::setLocale('en');
        }

        $request->merge(['lang' => $lang]);

        return $next($request);
    }
}
