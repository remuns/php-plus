<?php

namespace PhpPlus\Core\Types;

use PhpPlus\Core\Traits\SingletonTrait;

/**
 * A singleton type representing the root of the array inheritence hierarchy.
 */
final class BaseArrayType extends ArrayType implements PrimitiveTypeInterface
{
    use PrimitiveTypeTrait;
    use SingletonTrait;

    public function has($item): bool { return is_array($item); }

    public function __toString(): string { return 'array'; }
}
BaseArrayType::__constructStatic();
