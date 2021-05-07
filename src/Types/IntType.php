<?php

namespace PhpPlus\Core\Types;

use PhpPlus\Core\Traits\SingletonTrait;

/**
 * A class representing the PHP "integer" type.
 */
final class IntType extends Type implements PrimitiveTypeInterface
{
    use SingletonTrait;
    use SimplePrimitiveTypeTrait;

    public function typeIndicator(): int { return Type::INTEGER_INDICATOR; }

    /**
     * Gets the maximum possible value of the integer type.
     * @return int
     */
    public function max(): int { return PHP_INT_MAX; }

    /**
     * Gets the minimum possible value of the integer type.
     * @return int
     */
    public function min(): int { return PHP_INT_MIN; }

    /**
     * Gets the size of the integer type.
     * @return int
     */
    public function size(): int { return PHP_INT_SIZE; }

    public function has($item): bool { return is_int($item); }

    public function __toString(): string { return 'int'; }
}
IntType::__constructStatic();
