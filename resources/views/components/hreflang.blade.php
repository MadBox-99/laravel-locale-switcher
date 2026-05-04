@php
    /** @var array<string, string> $locales */
    $locales = config('locale-switcher.locales', []);
    /** @var string $defaultLocale */
    $defaultLocale = config('locale-switcher.default_locale', 'en');
    /** @var string $mode */
    $mode = config('locale-switcher.mode', 'cookie');
    /** @var string $routeName */
    $routeName = config('locale-switcher.route_name', 'language.switch');
    $appUrl = rtrim((string) config('app.url'), '/');
    $currentPath = request()->getPathInfo();

    $nonDefaultLocales = array_diff(array_keys($locales), [$defaultLocale]);
    if ($nonDefaultLocales && preg_match('#^/('.implode('|', $nonDefaultLocales).')(/.*)?$#', $currentPath, $m)) {
        $currentPath = $m[2] ?? '/';
        if ($currentPath === '') {
            $currentPath = '/';
        }
    }

    $buildUrl = function (string $code) use ($mode, $defaultLocale, $appUrl, $currentPath, $routeName): string {
        if ($mode === 'url_prefix') {
            return $code === $defaultLocale
                ? $appUrl . $currentPath
                : $appUrl . '/' . $code . $currentPath;
        }

        return route($routeName, ['locale' => $code]);
    };
@endphp
@foreach ($locales as $code => $label)
<link rel="alternate" hreflang="{{ $code }}" href="{{ $buildUrl($code) }}" />
@endforeach
<link rel="alternate" hreflang="x-default" href="{{ $buildUrl($defaultLocale) }}" />
