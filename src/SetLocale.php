<?php

declare(strict_types=1);

namespace MadBox\LocaleSwitcher;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

final class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var string $cookieName */
        $cookieName = config('locale-switcher.cookie_name', 'locale');

        /** @var array<string> $locales */
        $locales = config('locale-switcher.locales', []);

        $locale = $request->cookie($cookieName);

        if (is_string($locale) && in_array($locale, $locales, true)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
