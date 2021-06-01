<?php

namespace PhpPlus\Core\Tests\Types;

use PhpPlus\Core\Types\Type;
use PhpPlus\Core\Types\Types;

/**
 * Tests the Type::bool() type.
 */
class BoolTypeTest extends PrimitiveTypeTestCase
{
    /**
     * Gets the primitive type under test.
     * @return Type
     */
    protected function primitiveTypeTested(): Type { return Types::bool(); }

    /**
     * Tests type comparisons for the {@see Types::true()} type.
     */
    public function testTrueTypeComparison()
    {
        $this->assertSameType(Types::true(), Types::true());
        $this->assertSubtypeStrict(Types::true(), Types::bool());
        $this->assertSupertypeStrict(Types::bool(), Types::true());
    }

    /**
     * Tests containment for the {@see Types::true()} type.
     */
    public function testTrueTypeContainment()
    {
        $this->assertHas(Types::true(), true);
        $this->assertNotHas(Types::true(), false);
    }

    /**
     * Tests type comparisons for the {@see Types::false()} type.
     */
    public function testFalseTypeComparison()
    {
        $this->assertSameType(Types::false(), Types::false());
        $this->assertSubtypeStrict(Types::false(), Types::bool());
        $this->assertSupertypeStrict(Types::bool(), Types::false());
    }

    /**
     * Tests containment for the {@see Types::false()} type.
     */
    public function testFalseTypeContainment()
    {
        $this->assertHas(Types::false(), false);
        $this->assertNotHas(Types::false(), true);
    }
}
