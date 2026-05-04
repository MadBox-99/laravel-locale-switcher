<?php

declare(strict_types=1);

namespace MadBox\LocaleSwitcher;

use MadBox\LocaleSwitcher\Middleware\SetLocale as MiddlewareSetLocale;

/**
 * @deprecated since 1.1.0, use MadBox\LocaleSwitcher\Middleware\SetLocale instead.
 *             Kept for backward compatibility with v1.0.x consumers.
 */
final class SetLocale extends MiddlewareSetLocale
{
}
