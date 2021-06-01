<?php

namespace PhpPlus\Core\Tests\Types;

use PhpPlus\Core\Types\Type;
use PhpPlus\Core\Types\Types;

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
        $anything = Types::anything();
        $otherType =  Types::string();
        $this->assertSupertypeStrict($anything, $otherType);
        $this->assertSubtypeStrict($otherType, $anything);
        $this->assertSameType($anything, $anything);
    }

    /**
     * Tests subtyping rules for the "nothing" type.
     */
    public function testNothing()
    {
        $nothing = Types::nothing();
        $otherType =  Types::int();
        $this->assertSubtypeStrict($nothing, $otherType);
        $this->assertSupertypeStrict($otherType, $nothing);
        $this->assertSameType($nothing, $nothing); 
    }
}
