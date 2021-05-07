<?php

namespace PhpPlus\Core\Tests\Types;

use PhpPlus\Core\Types\Type;

/**
 * Tests the Type::bool() type.
 */
class BoolTypeTest extends PrimitiveTypeTestCase
{
    /**
     * Gets the primitive type under test.
     * @return Type
     */
    protected function primitiveTypeTested(): Type { return Type::bool(); }

    /**
     * Tests type comparisons for the {@see Type::true()} type.
     */
    public function testTrueTypeComparison()
    {
        $this->assertSameType(Type::true(), Type::true());
        $this->assertSubtypeStrict(Type::true(), Type::bool());
        $this->assertSupertypeStrict(Type::bool(), Type::true());
    }

    /**
     * Tests containment for the {@see Type::true()} type.
     */
    public function testTrueTypeContainment()
    {
        $this->assertHas(Type::true(), true);
        $this->assertNotHas(Type::true(), false);
    }

    /**
     * Tests type comparisons for the {@see Type::false()} type.
     */
    public function testFalseTypeComparison()
    {
        $this->assertSameType(Type::false(), Type::false());
        $this->assertSubtypeStrict(Type::false(), Type::bool());
        $this->assertSupertypeStrict(Type::bool(), Type::false());
    }

    /**
     * Tests containment for the {@see Type::false()} type.
     */
    public function testFalseTypeContainment()
    {
        $this->assertHas(Type::false(), false);
        $this->assertNotHas(Type::false(), true);
    }
}
