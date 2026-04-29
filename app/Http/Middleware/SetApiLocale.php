<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetApiLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = $request->header('X-Language')
            ?? $request->header('Accept-Language')
            ?? optional(auth()->user())->language
            ?? 'ms';

        $locale = str_starts_with($locale, 'en') ? 'en' : 'ms';
        app()->setLocale($locale);

        return $next($request);
    }
}
