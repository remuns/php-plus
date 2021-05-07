<?php

namespace PhpPlus\Core\Types;

/**
 * An interface for non-trivial types (i.e. not anything or nothing).
 */
interface NonTrivialTypeInterface
{
    /**
     * Get the standard PHP type returned by gettype() calls to objects of the type.
     */
    public function baseType(): PrimitiveTypeInterface;
}
