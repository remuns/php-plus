<?php

namespace PhpPlus\Core\Types;

use PhpPlus\Core\Traits\SingletonTrait;

/**
 * A class representing the PHP "string" type.
 */
final class StringType extends Type implements PrimitiveTypeInterface
{
    use SingletonTrait;
    use SimplePrimitiveTypeTrait;

    public function typeIndicator(): int { return Type::STRING_INDICATOR; }

    public function has($item): bool { return is_string($item); }

    public function __toString(): string { return 'string'; }
}
StringType::__constructStatic();
