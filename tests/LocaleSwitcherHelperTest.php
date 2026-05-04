<?php

declare(strict_types=1);

use Illuminate\Support\Facades\App;
use MadBox\LocaleSwitcher\LocaleSwitcher;

beforeEach(function (): void {
    config()->set('locale-switcher.locales', ['en' => 'English', 'hu' => 'Magyar', 'de' => 'Deutsch']);
    config()->set('locale-switcher.default_locale', 'hu');
    config()->set('app.url', 'http://localhost');
});

it('returns the configured locales', function (): void {
    expect(LocaleSwitcher::available())
        ->toBe(['en' => 'English', 'hu' => 'Magyar', 'de' => 'Deutsch']);
});

it('returns the configured default locale', function (): void {
    expect(LocaleSwitcher::default())->toBe('hu');
});

it('returns the configured mode', function (): void {
    config()->set('locale-switcher.mode', 'url_prefix');

    expect(LocaleSwitcher::mode())->toBe('url_prefix');
});

it('returns the current app locale', function (): void {
    App::setLocale('en');

    expect(LocaleSwitcher::current())->toBe('en');
});

it('builds a locale-prefixed URL in url_prefix mode for non-default locales', function (): void {
    config()->set('locale-switcher.mode', 'url_prefix');

    expect(LocaleSwitcher::urlFor('en', '/foo'))->toBe('http://localhost/en/foo');
});

it('omits the prefix for the default locale in url_prefix mode', function (): void {
    config()->set('locale-switcher.mode', 'url_prefix');

    expect(LocaleSwitcher::urlFor('hu', '/foo'))->toBe('http://localhost/foo');
});

it('returns the switch route URL in cookie mode', function (): void {
    config()->set('locale-switcher.mode', 'cookie');

    expect(LocaleSwitcher::urlFor('en', '/foo'))
        ->toContain('/language/en');
});

it('returns alternates with an x-default key', function (): void {
    config()->set('locale-switcher.mode', 'url_prefix');

    $alternates = LocaleSwitcher::alternates('/foo');

    expect($alternates)
        ->toHaveKeys(['en', 'hu', 'de', 'x-default'])
        ->and($alternates['en'])->toBe('http://localhost/en/foo')
        ->and($alternates['hu'])->toBe('http://localhost/foo')
        ->and($alternates['de'])->toBe('http://localhost/de/foo')
        ->and($alternates['x-default'])->toBe('http://localhost/foo');
});
