<?php

declare(strict_types=1);

namespace MoonShine\Socialite\Exceptions;

use RuntimeException;

final class AuthException extends RuntimeException
{
    public static function driverNotFound(): self
    {
        return new self('Driver not found in config file');
    }
}
