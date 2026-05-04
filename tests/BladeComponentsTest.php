<?php

declare(strict_types=1);

beforeEach(function (): void {
    config()->set('locale-switcher.locales', ['en' => 'English', 'hu' => 'Magyar', 'de' => 'Deutsch']);
    config()->set('locale-switcher.default_locale', 'hu');
    config()->set('app.url', 'http://localhost');
});

it('renders the dropdown view in url_prefix mode', function (): void {
    config()->set('locale-switcher.mode', 'url_prefix');

    $html = view('locale-switcher::components.dropdown')->render();

    expect($html)
        ->toContain('http://localhost/en')
        ->toContain('http://localhost/de');
});

it('renders the dropdown view in cookie mode using the switch route', function (): void {
    config()->set('locale-switcher.mode', 'cookie');

    $html = view('locale-switcher::components.dropdown')->render();

    expect($html)->toContain('/language/en');
});

it('renders hreflang tags for every locale plus x-default in url_prefix mode', function (): void {
    config()->set('locale-switcher.mode', 'url_prefix');

    $html = view('locale-switcher::components.hreflang')->render();

    expect($html)
        ->toContain('hreflang="en"')
        ->toContain('hreflang="hu"')
        ->toContain('hreflang="de"')
        ->toContain('hreflang="x-default"')
        ->toContain('http://localhost/en')
        ->toContain('http://localhost/de');
});

it('renders hreflang tags in cookie mode', function (): void {
    config()->set('locale-switcher.mode', 'cookie');

    $html = view('locale-switcher::components.hreflang')->render();

    expect($html)
        ->toContain('hreflang="en"')
        ->toContain('hreflang="x-default"');
});
