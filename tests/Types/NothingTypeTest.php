<?php

namespace PhpPlus\Core\Tests\Types;

use PhpPlus\Core\Types\Type;

/**
 * Tests for the "nothing" type.
 */
class NothingTypeTest extends TypeTestCase
{
    /**
     * Tests comparison rules for the "nothing" type.
     */
    public function testCompare()
    {
        $nothing = Type::nothing();
        $otherType =  Type::string();
        $this->assertTrue($nothing->compare($otherType) < 0);
        $this->assertTrue($otherType->compare($nothing) > 0);
        $this->assertEquals(0, $nothing->compare($nothing));
    }

    /**
     * Tests type-checking for the "nothing" type.
     */
    public function testCheck()
    {
        $nothing = Type::nothing();

        // No value should be an instance of this type
        $this->assertFalse($nothing->has(4));
        $this->assertFalse($nothing->has('abc'));
        $this->assertFalse($nothing->has(true));
        $this->assertFalse($nothing->has(null));
        $this->assertFalse($nothing->has(4.5));
        $this->assertFalse($nothing->has([1, 2, 3]));
        $this->assertFalse($nothing->has(new \stdClass));
    }
}
