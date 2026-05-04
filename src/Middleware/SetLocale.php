<?php

declare(strict_types=1);

namespace MadBox\LocaleSwitcher\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use MadBox\LocaleSwitcher\LocaleSwitcher;
use ReflectionProperty;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locales = LocaleSwitcher::available();
        $cookieName = (string) config('locale-switcher.cookie_name', 'locale');

        if (LocaleSwitcher::mode() !== LocaleSwitcher::MODE_URL_PREFIX) {
            $cookie = $request->cookie($cookieName);

            if (is_string($cookie) && array_key_exists($cookie, $locales)) {
                App::setLocale($cookie);
            }

            return $next($request);
        }

        $defaultLocale = LocaleSwitcher::default();

        $locale = $request->attributes->get(LocaleSwitcher::REQUEST_ATTRIBUTE);

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
            $appUrl = LocaleSwitcher::appUrl();

            // Preserve the unprefixed asset root so Vite/asset() URLs do not
            // get the locale prefix when forceRootUrl rewrites the route root.
            $urlGenerator = app('url');
            (new ReflectionProperty($urlGenerator, 'assetRoot'))->setValue($urlGenerator, $appUrl);

            URL::forceRootUrl($appUrl.'/'.$locale);
        }

        return $next($request);
    }
}
