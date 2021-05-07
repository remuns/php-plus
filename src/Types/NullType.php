<?php

namespace PhpPlus\Core\Types;

use PhpPlus\Core\Traits\SingletonTrait;

/**
 * A class representing the PHP "NULL" type.
 */
final class NullType extends Type implements PrimitiveTypeInterface
{
    use SingletonTrait;
    use SimplePrimitiveTypeTrait;
    use SingleValueTypeTrait;

    public function typeIndicator(): int { return Type::NULL_INDICATOR; }

    public function singleValue(): mixed { return null; }

    public function __toString(): string { return 'null'; }
}
NullType::__constructStatic();
