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
        return $this->typeIndicator() === $other->typeIndicator() ?
                0 :
                $other->handleTrivialCompareCases();
    }

    public abstract function typeIndicator(): int;
}
