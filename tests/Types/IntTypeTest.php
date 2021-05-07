<?php

namespace PhpPlus\Core\Tests\Types;

use PhpPlus\Core\Types\Type;

/**
 * Tests the Type::int() type.
 */
class IntTypeTest extends PrimitiveTypeTestCase
{
    /**
     * Gets the primitive type under test.
     * @return Type
     */
    protected function primitiveTypeTested(): Type { return Type::int(); }
}
