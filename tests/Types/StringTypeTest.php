<?php

namespace PhpPlus\Core\Tests\Types;

use PhpPlus\Core\Types\Type;

/**
 * Tests the Type::float() type.
 */
class StringTypeTest extends PrimitiveTypeTestCase
{
    /**
     * Gets the primitive type under test.
     * @return Type
     */
    protected function primitiveTypeTested(): Type { return Type::string(); }
}
