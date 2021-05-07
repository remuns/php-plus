<?php

namespace PhpPlus\Core\Types;

use PhpPlus\Core\Traits\SingletonTrait;

/**
 * A class representing the PHP "boolean" type.
 */
final class BaseBoolType extends BoolType implements PrimitiveTypeInterface
{
    use SingletonTrait;
    use PrimitiveTypeTrait;

    public function compare(Type $other): ?int
    {
        if ($other->typeIndicator() === Type::BOOLEAN_INDICATOR) {
            return $other == $this ? 0 : 1;
        } else {
            return $other->handleTrivialCompareCases();
        }
    }

    public function __toString(): string { return 'bool'; }

    public function has($item): bool { return is_bool($item); }
}
BaseBoolType::__constructStatic();
