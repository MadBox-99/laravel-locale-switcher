<?php

declare(strict_types=1);

namespace MadBox\LocaleSwitcher\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use ReflectionProperty;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var array<string, string> $locales */
        $locales = config('locale-switcher.locales', []);

        /** @var string $cookieName */
        $cookieName = config('locale-switcher.cookie_name', 'locale');

        /** @var string $mode */
        $mode = config('locale-switcher.mode', 'cookie');

        if ($mode === 'url_prefix') {
            /** @var string $defaultLocale */
            $defaultLocale = config('locale-switcher.default_locale', 'en');

            $locale = $request->attributes->get('_locale');

            if (! $locale) {
                $cookie = $request->cookie($cookieName);

                if (is_string($cookie) && array_key_exists($cookie, $locales)) {
                    $locale = $cookie;
                }
            }

            if (! is_string($locale) || ! array_key_exists($locale, $locales)) {
                $locale = $defaultLocale;
            }

            App::setLocale($locale);

            if ($locale !== $defaultLocale) {
                $appUrl = rtrim((string) config('app.url'), '/');

                $urlGenerator = app('url');
                $assetProp = new ReflectionProperty($urlGenerator, 'assetRoot');
                $assetProp->setValue($urlGenerator, $appUrl);

                URL::forceRootUrl($appUrl.'/'.$locale);
            }

            return $next($request);
        }

        // Cookie mode (default, backward compatible).
        $locale = $request->cookie($cookieName);

        if (is_string($locale) && array_key_exists($locale, $locales)) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
