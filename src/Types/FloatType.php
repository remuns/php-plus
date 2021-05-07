<?php

namespace PhpPlus\Core\Types;

use PhpPlus\Core\Traits\SingletonTrait;

/**
 * A class representing the PHP "double" (float) type.
 */
final class FloatType extends Type implements PrimitiveTypeInterface
{
    use SingletonTrait;
    use SimplePrimitiveTypeTrait;

    public function typeIndicator(): int { return Type::FLOAT_INDICATOR; }

    /**
     * Gets the maximum possible value of the float type.
     * @return float
     */
    public function max(): float { return PHP_FLOAT_MAX; }

    /**
     * Gets the minimum possible positive value of the float type.
     * @return float
     */
    public function minPositive(): float { return PHP_FLOAT_MIN; }

    /**
     * Gets the maximum possible negative value of the float type.
     * @return float
     */
    public function maxNegative(): float { return -PHP_FLOAT_MIN; }

    /**
     * Gets the minimum possible value of the float type.
     * @return float
     */
    public function min(): float { return -PHP_FLOAT_MAX; }

    /**
     * Gets the smallest representable positive float x, so that x + 1.0 != 1.0.
     * @return float
     */
    public function epsilon(): float { return PHP_FLOAT_EPSILON; }

    /**
     * Gets the number of decimal digits that can be rounded into a float and back without
     * precision loss.
     * @return int
     */
    public function dig(): int { return PHP_FLOAT_DIG; }

    public function has($item): bool { return is_float($item); }

    public function __toString(): string { return 'float'; }
}
FloatType::__constructStatic();
