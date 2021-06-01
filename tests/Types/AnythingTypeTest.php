<?php

namespace PhpPlus\Core\Tests\Types;

use PhpPlus\Core\Types\Type;
use PhpPlus\Core\Types\Types;

/**
 * Tests for the "anything" type.
 */
class AnythingTypeTest extends TypeTestCase
{
    /**
     * Tests comparison rules for the "anything" type.
     */
    public function testCompare()
    {
        $anything = Types::anything();
        $otherType =  Types::string();
        $this->assertTrue($anything->compare($otherType) > 0);
        $this->assertTrue($otherType->compare($anything) < 0);
        $this->assertEquals(0, $anything->compare($anything));
    }

    /**
     * Tests type-checking for the "anything" type.
     */
    public function testCheck()
    {
        $anything = Types::anything();

        // Any value should be an instance of this type
        $this->assertTrue($anything->has(4));
        $this->assertTrue($anything->has('abc'));
        $this->assertTrue($anything->has(true));
        $this->assertTrue($anything->has(null));
        $this->assertTrue($anything->has(4.5));
        $this->assertTrue($anything->has([1, 2, 3]));
        $this->assertTrue($anything->has(new \stdClass));
    }
}
