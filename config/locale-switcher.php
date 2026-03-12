<?php

declare(strict_types=1);

return [
    /*
     * The supported locales as locale => label pairs.
     * Only these locale codes will be accepted.
     */
    'locales' => [
        'en' => 'English',
        'hu' => 'Magyar',
    ],

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
