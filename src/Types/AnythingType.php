<?php

namespace PhpPlus\Core\Types;

use PhpPlus\Core\Traits\SingletonTrait;

/**
 * A type representing the root of the type hierarchy.
 * Any value is an instance of this type.
 */
final class AnythingType extends Type
{
    use SingletonTrait;

    public function isStrictSubtypeOf(Type $other): bool
    {
        return false;
    }

    public function compare(Type $other): ?int
    {
        return $this == $other ? 0 : 1;
    }

    public function typeIndicator(): int { return Type::ANYTHING_INDICATOR; }

    public function handleTrivialCompareCases(): int { return -1; }

    public function has($item): bool { return true; }

    public function __toString(): string { return 'anything'; }
}
AnythingType::__constructStatic();
