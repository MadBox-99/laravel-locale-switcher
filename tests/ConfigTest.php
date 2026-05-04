<?php

declare(strict_types=1);

it('defaults to cookie mode', function (): void {
    expect(config('locale-switcher.mode'))->toBe('cookie');
});

it('exposes the configured locales', function (): void {
    expect(config('locale-switcher.locales'))
        ->toBeArray()
        ->toHaveKeys(['hu', 'en', 'de']);
});

it('defines a default locale', function (): void {
    expect(config('locale-switcher.default_locale'))->toBe('hu');
});

it('defines the cookie name and lifetime', function (): void {
    expect(config('locale-switcher.cookie_name'))->toBe('locale')
        ->and(config('locale-switcher.cookie_lifetime'))->toBe(60 * 24 * 365);
});

it('defines a route name and prefix', function (): void {
    expect(config('locale-switcher.route_name'))->toBe('language.switch')
        ->and(config('locale-switcher.route_prefix'))->toBe('/language');
});
