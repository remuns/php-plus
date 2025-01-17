<?php

namespace PhpPlus\Core\Types;

use PhpPlus\Core\Traits\SingletonTrait;

/**
 * A type representing the root of the class hierarchy.
 * This type does not directly have any instances, but all class instances are subtypes of it.
 */
final class BaseObjectType extends ObjectType implements PrimitiveTypeInterface
{
    use SingletonTrait;
    use NonTrivialTypeTrait;
    use PrimitiveTypeTrait;

    public function __toString(): string { return 'object'; }

    public function has($item): bool { return is_object($item); }
}
BaseObjectType::__constructStatic();
