<?php

declare(strict_types=1);

namespace MadBox\LocaleSwitcher\Middleware;

use Closure;
use Illuminate\Http\Request;
use MadBox\LocaleSwitcher\LocaleSwitcher;
use Symfony\Component\HttpFoundation\Response;

final class StripLocalePrefix
{
    public function handle(Request $request, Closure $next): Response
    {
        if (LocaleSwitcher::mode() !== LocaleSwitcher::MODE_URL_PREFIX) {
            return $next($request);
        }

        $path = $request->getPathInfo();

        $locales = LocaleSwitcher::available();
        $defaultLocale = LocaleSwitcher::default();
        $nonDefaultLocales = array_diff(array_keys($locales), [$defaultLocale]);

        if ($nonDefaultLocales === [] || ! preg_match('#^/('.implode('|', $nonDefaultLocales).')(/.*)?$#', $path, $matches)) {
            return $next($request);
        }

        $locale = $matches[1];
        $newPath = $matches[2] ?? '/';

        if ($newPath === '') {
            $newPath = '/';
        }

        $request->attributes->set(LocaleSwitcher::REQUEST_ATTRIBUTE, $locale);

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

        return $next($request);
    }
}
