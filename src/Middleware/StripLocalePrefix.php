<?php

declare(strict_types=1);

namespace MadBox\LocaleSwitcher\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Global middleware that runs BEFORE routing.
 * Strips a non-default locale prefix (e.g. /en, /de) from the request path so
 * routes match normally, and stores the detected locale in the request
 * attribute `_locale` for downstream middleware.
 *
 * Only active when locale-switcher.mode === 'url_prefix'.
 */
final class StripLocalePrefix
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('locale-switcher.mode') !== 'url_prefix') {
            return $next($request);
        }

        $path = $request->getPathInfo();

        /** @var array<string, string> $locales */
        $locales = config('locale-switcher.locales', []);

        /** @var string $defaultLocale */
        $defaultLocale = config('locale-switcher.default_locale', 'en');

        $nonDefaultLocales = array_diff(array_keys($locales), [$defaultLocale]);

        if ($nonDefaultLocales && preg_match('#^/('.implode('|', $nonDefaultLocales).')(/.*)?$#', $path, $matches)) {
            $locale = $matches[1];
            $newPath = $matches[2] ?? '/';

            if ($newPath === '') {
                $newPath = '/';
            }

            $request->attributes->set('_locale', $locale);

            $queryString = $request->getQueryString();
            $newUri = $newPath.($queryString ? '?'.$queryString : '');

            $request->server->set('REQUEST_URI', $newUri);

            $request->initialize(
                $request->query->all(),
                $request->request->all(),
                $request->attributes->all(),
                $request->cookies->all(),
                $request->files->all(),
                $request->server->all(),
                $request->getContent()
            );
        }

        return $next($request);
    }
}
