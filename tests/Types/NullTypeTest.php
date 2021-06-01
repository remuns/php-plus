<?php

namespace PhpPlus\Core\Tests\Types;

use PhpPlus\Core\Types\Type;
use PhpPlus\Core\Types\Types;

/**
 * Tests the Type::int() type.
 */
class NullTypeTest extends PrimitiveTypeTestCase
{
    /**
     * Gets the primitive type under test.
     * @return Type
     */
    protected function primitiveTypeTested(): Type { return Types::null(); }
}
