<?php

namespace PhpPlus\Core\Tests\Types;

use PhpPlus\Core\Types\Type;

/**
 * General tests for PHP types.
 */
class GeneralTest extends TypeTestCase
{
    /**
     * Tests subtyping rules for the "anything" type.
     */
    public function testAnything()
    {
        $anything = Type::anything();
        $otherType =  Type::string();
        $this->assertSupertypeStrict($anything, $otherType);
        $this->assertSubtypeStrict($otherType, $anything);
        $this->assertSameType($anything, $anything);
    }

    /**
     * Tests subtyping rules for the "nothing" type.
     */
    public function testNothing()
    {
        $nothing = Type::nothing();
        $otherType =  Type::int();
        $this->assertSubtypeStrict($nothing, $otherType);
        $this->assertSupertypeStrict($otherType, $nothing);
        $this->assertSameType($nothing, $nothing); 
    }
}
