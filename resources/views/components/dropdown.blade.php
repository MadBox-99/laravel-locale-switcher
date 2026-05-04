{{--
    Locale switcher dropdown component.
    Requires Tailwind CSS and Alpine.js in the consuming application.
    Honors locale-switcher.mode: in url_prefix mode links use locale-prefixed URLs;
    in cookie mode they hit the configured switch route.
--}}
@php
    $currentLocale = app()->getLocale();
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

    // Strip an existing non-default locale prefix from the current path so we
    // can build clean alternate URLs.
    $nonDefaultLocales = array_diff(array_keys($locales), [$defaultLocale]);
    if ($nonDefaultLocales && preg_match('#^/('.implode('|', $nonDefaultLocales).')(/.*)?$#', $currentPath, $m)) {
        $currentPath = $m[2] ?? '/';
        if ($currentPath === '') {
            $currentPath = '/';
        }
    }
@endphp

<div class="relative" x-data="{ open: false }" @click.away="open = false">
    <button @click="open = !open"
        class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium border rounded-full transition-colors text-gray-700 hover:text-gray-900 border-gray-300">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 003 12c0-1.605.42-3.113 1.157-4.418" />
        </svg>
        <span class="uppercase">{{ $currentLocale }}</span>
        <svg class="w-3 h-3" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div x-show="open" x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-y-1"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-1"
        x-cloak
        class="absolute right-0 top-full mt-2 w-40 bg-white rounded-xl shadow-xl border border-gray-100 py-1.5 z-50">
        @foreach ($locales as $code => $label)
            @php
                if ($mode === 'url_prefix') {
                    $url = $code === $defaultLocale
                        ? $appUrl . $currentPath
                        : $appUrl . '/' . $code . $currentPath;
                } else {
                    $url = route($routeName, ['locale' => $code]);
                }
            @endphp
            <a href="{{ $url }}"
                @click="open = false"
                class="flex items-center gap-2.5 px-4 py-2 text-sm transition-colors {{ $code === $currentLocale ? 'text-primary-600 bg-primary-50 font-medium' : 'text-gray-700 hover:bg-gray-50' }}">
                <span class="w-5 text-center text-xs font-semibold uppercase {{ $code === $currentLocale ? 'text-primary-500' : 'text-gray-400' }}">{{ $code }}</span>
                {{ $label }}
                @if ($code === $currentLocale)
                    <svg class="ml-auto w-4 h-4 text-primary-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                @endif
            </a>
        @endforeach
    </div>
</div>
