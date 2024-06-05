<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
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
        $locale = $request->lang ?? 'en';

        if (!in_array($locale, ['en', 'id'])) {
            return response()->json([
                'error' => 'Invalid language.'
            ], 400);
        }

        return $next($request);
    }
}
