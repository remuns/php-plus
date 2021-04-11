<?php

namespace PhpPlus\Core\Traits;

use PhpPlus\Core\Exceptions\InvalidOperationException;

/**
 * A trait for classes that should not have inaccessible or non-existent properties set.
 */
trait WellDefinedSelf
{
    public function __set(string $name, $value)
    {
        // Throw an exception to prevent the property from being set
        throw new InvalidOperationException('cannot set private or unspecified property');
    }
}
