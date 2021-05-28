<?php

namespace PhpPlus\Core\Tests\Types;

use PhpPlus\Core\Types\Type;

/**
 * Tests the Type::array() type.
 */
class ArrayTypeTest extends PrimitiveTypeTestCase
{
    /**
     * Gets the primitive type under test.
     * @return Type
     */
    protected function primitiveTypeTested(): Type { return Type::array(); }
}
