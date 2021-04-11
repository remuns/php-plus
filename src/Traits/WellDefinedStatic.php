<?php

namespace PhpPlus\Core\Traits;

use PhpPlus\Core\Exceptions\InvalidOperationException;

/**
 * A trait for classes that should not have inaccessible or non-existent properties set, and
 * should force that restriction on any child classes as well.
 */
trait WellDefinedStatic
{
    public final function __set(string $name, $value)
    {
        // Throw an exception to prevent the property from being set
        throw new InvalidOperationException('cannot set private or unspecified property');
    }
}
