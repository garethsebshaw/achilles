<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supportedLocales = array_keys(config('app.supported_locales', []));
        $requestedLocale = $request->query('locale');
        $sessionLocale = null;

        if ($request->hasSession()) {
            $sessionLocale = $request->session()->get('locale');
        }

        if (in_array($requestedLocale, $supportedLocales, true)) {
            $locale = $requestedLocale;

            if ($request->hasSession()) {
                $request->session()->put('locale', $requestedLocale);
            }
        } elseif (in_array($sessionLocale, $supportedLocales, true)) {
            $locale = $sessionLocale;
        } else {
            $locale = $request->getPreferredLanguage($supportedLocales) ?: config('app.fallback_locale');
        }

        app()->setLocale($locale);
        Carbon::setLocale($locale);

        return $next($request);
    }
}
