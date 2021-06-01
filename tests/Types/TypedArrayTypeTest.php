<?php

namespace PhpPlus\Core\Tests\Types
{
    use PhpPlus\Core\Tests\Types\TypedArrayTypeTest\{ A, B };
    use PhpPlus\Core\Types\ClassType;
    use PhpPlus\Core\Types\TypedArrayType;
    use PhpPlus\Core\Types\Types;

    /**
     * Tests the {@see TypedArrayType} class.
     */
    class TypedArrayTypeTest extends TypeTestCase
    {
        /**
         * Tests the {@see TypedArrayType::has()} method.
         */
        public function testHas()
        {
            $strArrType = Types::array(Types::string());
            $arrTypeA = Types::array(self::aType());
            $arrTypeB = Types::array(self::bType());

            // Should have an array with all strings
            $this->assertHas($strArrType, ['g', 'h', 'i', 'j', '333']);

            // Any violation of "all strings" destroys containment
            $this->assertNotHas($strArrType, ['r', 's', 't', 555]);

            // Should reject anything with no strings
            $this->assertNotHas($strArrType, [null, 45, 1.2, false]);

            // Should reject anything that isn't an array
            $this->assertNotHas($strArrType, new \stdClass);

            // Should accept arrays with strict subtypes
            $this->assertHas($arrTypeA, [new B, new B]);
            
            // Should reject arrays with strict supertypes
            $this->assertNotHas($arrTypeB, [new A, new B]);
        }

        /**
         * Tests the {@see TypedArrayType::compare()} method.
         */
        public function testComparison()
        {
            // All array types are a subtype of the base array type
            $this->assertSupertypeStrict(Types::array(), Types::array(Types::string()));
            $this->assertSubtypeStrict(Types::array(Types::null()), Types::array());

            // Typed array types should respect the comparisons of their wrapped types
            $this->assertSupertypeStrict(Types::array(self::aType()), Types::array(self::bType()));
            $this->assertSubtypeStrict(Types::array(self::bType()), Types::array(self::aType()));
            $this->assertIncomparable(Types::array(Types::string()), Types::array(Types::null()));

            // Anything that is not an array type should not compare
            $this->assertIncomparable(Types::array(Types::string()), Types::string());
        }

        protected static function aType(): ClassType { return new ClassType(A::class); }
        protected static function bType(): ClassType { return new ClassType(B::class); }
    }
}

namespace PhpPlus\Core\Tests\Types\TypedArrayTypeTest
{
    class A { }
    class B extends A { }
}
