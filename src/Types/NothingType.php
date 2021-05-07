<?php

namespace PhpPlus\Core\Types;

use PhpPlus\Core\Traits\SingletonTrait;

/**
 * A type representing the bottom of the type hierarchy.
 * No value is an instance of this type, and it is a subtype of every type.
 * 
 * The only way for a function to have this as a return type is if the function throws an
 * exception in every case (non-exceptional cases should in turn cause a type error to
 * be thrown).
 */
final class NothingType extends Type
{
    use SingletonTrait;

    public function isStrictSupertypeOf(Type $other): bool
    {
        return false;
    }

    public function compare(Type $other): ?int
    {
        return $this == $other ? 0 : -1;
    }

    public function typeIndicator(): int { return Type::NOTHING_INDICATOR; }

    public function handleTrivialCompareCases(): int { return 1; }

    public function __toString(): string { return 'nothing'; }

    public function has($item): bool { return false; }
}
NothingType::__constructStatic();
