<?php

declare(strict_types=1);

return [
    /*
     * The supported locales as locale => label pairs.
     * Only these locale codes will be accepted.
     */
    'locales' => [
        'hu' => 'Magyar',
        'en' => 'English',
        'de' => 'Deutsch',
    ],

    /*
     * The default locale. In url_prefix mode this locale is served at the
     * un-prefixed root and is omitted from generated URLs.
     */
    'default_locale' => 'hu',

    /*
     * Locale resolution strategy.
     *
     * - 'cookie'     : locale is stored in a cookie (default, backward compatible).
     * - 'url_prefix' : locale is taken from the URL path prefix (e.g. /en/foo).
     */
    'mode' => env('LOCALE_SWITCHER_MODE', 'cookie'),

    /*
     * The name of the cookie used to store the selected locale.
     */
    'cookie_name' => 'locale',

    /*
     * Cookie lifetime in minutes. Default: 1 year.
     */
    'cookie_lifetime' => 60 * 24 * 365,

    /*
     * The route name for the language switcher.
     */
    'route_name' => 'language.switch',

    /*
     * The route URI prefix for the language switcher.
     */
    'route_prefix' => '/language',
];
