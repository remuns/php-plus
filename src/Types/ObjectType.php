<?php

namespace PhpPlus\Core\Types;

/**
 * An abstract base class for all types that are considered to be object types.
 * This includes class and any interface or trait types included in the library, as well as
 * the non-concrete object type.
 * @see ClassType
 * @see BaseObjectType
 */
abstract class ObjectType extends Type
{
    use NonTrivialTypeTrait;

    public function typeIndicator(): int { return Type::OBJECT_INDICATOR; }
}
