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

    public function __toString(): string { return 'bool'; }

    public function has($item): bool { return is_bool($item); }
}
BaseBoolType::__constructStatic();
