<?php

namespace PhpPlus\Core\Types;

/**
 * A base type for booleans.
 * This class is necessary to allow for the general boolean type, as well as specific boolean
 * types (i.e. true, false).
 */
abstract class BoolType extends Type implements NonTrivialTypeInterface
{
    use NonTrivialTypeTrait;
    public function typeIndicator(): int { return Type::BOOLEAN_INDICATOR; }

    public function baseType(): PrimitiveTypeInterface { return BaseBoolType::value(); }
}
