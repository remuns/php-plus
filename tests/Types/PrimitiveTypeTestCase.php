<?php

namespace PhpPlus\Core\Tests\Types;

use PhpPlus\Core\Types\Type;
use PhpPlus\Core\Types\Types;

abstract class PrimitiveTypeTestCase extends TypeTestCase
{
    /**
     * Tests comparisons between the type under test and other primitive types.
     */
    public function testPrimitiveCompare()
    {
        foreach ($this->primitiveTypes() as $t) {
            if ($t == $this->primitiveTypeTested()) {
                $this->assertSameType($this->primitiveTypeTested(), $t);
                $this->assertSameType($t, $this->primitiveTypeTested());
            } else {
                $this->assertIncomparable($t, $this->primitiveTypeTested());
            }
        }
    }

    /**
     * Gets an array containing the primitive types.
     * @return array
     */
    protected final function primitiveTypes(): array
    {
        return [
            Types::bool(),
            Types::float(),
            Types::int(),
            Types::null(),
            Types::object(),
            Types::string(),
        ];
    }

    /**
     * Gets the primitive type under test.
     * @return Type
     */
    protected abstract function primitiveTypeTested(): Type;
}
