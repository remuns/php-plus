<?php

namespace PhpPlus\Core\Types;

/**
 * An abstract class representing an array type.
 */
abstract class ArrayType extends Type
{
    public function typeIndicator(): int { return Type::ARRAY_INDICATOR; }
}
