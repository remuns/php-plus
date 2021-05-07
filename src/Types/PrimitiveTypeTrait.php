<?php

namespace PhpPlus\Core\Types;

/**
 * A trait for classes representing a PHP primitive type.
 */
trait PrimitiveTypeTrait
{
    use NonTrivialTypeTrait;
    public function baseType(): self { return $this; }

    public function compare(Type $other): ?int
    {
        if ($this->typeIndicator() == $other->typeIndicator()) {
            return $this == $other ? 0 : 1;
        } else {
            return $other->handleTrivialCompareCases();
        }
    }

    public abstract function typeIndicator(): int;
}
