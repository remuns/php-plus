<?php

namespace PhpPlus\Core\Types;

/**
 * A trait for classes representing a PHP primitive type.
 */
trait PrimitiveTypeTrait
{
    use NonTrivialTypeTrait;
    public function baseType(): self { return $this; }
}
