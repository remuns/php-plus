<?php

namespace PhpPlus\Core\Types;

use PhpPlus\Core\Traits\StaticClassTrait;

/**
 * A class for helper methods for the PHP+ type system.
 */
final class Helpers
{
    use StaticClassTrait;

    /**
     * Checks whether or not the value passed in is an instance of the type passed in.
     * 
     * @param Type  $type   The type to check for.
     * @param mixed $value  The value to check as an instance of the type passed in.
     * 
     * @return bool
     */
    public static function is(Type $type, mixed $value): bool
    {
        return $type->has($value);
    }
}
