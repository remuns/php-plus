<?php

namespace PhpPlus\Core\Types;

/**
 * A trait for classes representing simple primitive types that can easily be compared because
 * they have no non-trivial subtypes (i.e. int, string).
 */
trait SimplePrimitiveTypeTrait
{
    use PrimitiveTypeTrait;
    
    public function compare(Type $other): ?int
    {
        if ($this->typeIndicator() == $other->typeIndicator()) {
            return 0;
        } else {
            return $other->handleTrivialCompareCases();
        }
    }

    public abstract function typeIndicator(): int;
}
