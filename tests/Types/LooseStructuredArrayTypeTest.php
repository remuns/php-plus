<?php

namespace PhpPlus\Core\Tests\Types
{
    use PhpPlus\Core\Tests\Types\LooseStructuredArrayTypeTest\{A, B};
    use PhpPlus\Core\Types\LooseStructuredArrayType;
    use PhpPlus\Core\Types\Types;

    class LooseStructuredArrayTypeTest extends TypeTestCase
    {
        public function testHas_noExtras()
        {
            $arrType = new LooseStructuredArrayType([
                Types::null(), Types::int(),
                Types::class(A::class), Types::class(B::class),
            ], extraKeysType: Types::nothing());

            $this->assertHas($arrType, [null, 1, new A, new B]);
            $this->assertHas($arrType, [null, 0, new B, new B]);
            $this->assertHas($arrType, [1 => 1, 0 => null, new A, new B]);
            $this->assertNotHas($arrType, [null, 1, new A, new B, '4']);
            $this->assertNotHas($arrType, [null, 1, new A, new A]);
            $this->assertNotHas($arrType, [1 => null, 0 => null, new B, new B]);
        }

        public function testHas_allExtras()
        {
            $arrType = new LooseStructuredArrayType([
                Types::string(), Types::class(A::class),
            ], extraKeysType: Types::anything());

            $this->assertHas($arrType, ['a', new A, 's', 55, 1.2, null, true]);
            $this->assertNotHas($arrType, ['a', 5, 'g', null, true, 5.6]); // Wrong index 1
        }

        public function testHas_someExtras()
        {
            $arrType = new LooseStructuredArrayType([
                Types::string(), Types::class(A::class),
            ], extraKeysType: Types::class(B::class));

            $this->assertHas($arrType, ['5', new A, new B, new B]);
            $this->assertNotHas($arrType, [4, new A, new B]);  // Wrong index 0
            $this->assertNotHas($arrType, ['g', new A, new B, new A]); // Wrong (extra) index 3
        }

        public function testCompare()
        {
            $arrType1 = new LooseStructuredArrayType([
                Types::array(), Types::string(), Types::class(A::class),
            ], extraKeysType: Types::anything());

            $this->assertSameType($arrType1, $arrType1);

            // Key order doesn't matter
            $this->assertSameType(
                $arrType1,
                new LooseStructuredArrayType([
                    1 => Types::string(), 0 => Types::array(), Types::class(A::class),
                ], extraKeysType: Types::anything()));

            $this->assertSupertypeStrict(
                $arrType1,
                new LooseStructuredArrayType([
                    Types::array(Types::int()), Types::string(), Types::class(B::class),
                ], extraKeysType: Types::anything()));
            
            // Key order doesn't matter
            $this->assertSupertypeStrict(
                $arrType1,
                new LooseStructuredArrayType([
                    1 => Types::string(), 0 => Types::array(Types::int()), Types::class(A::class)
                ], extraKeysType: Types::anything()));

            $this->assertSubtypeStrict(
                $arrType1,
                new LooseStructuredArrayType([
                    Types::array(), Types::string(),
                ], extraKeysType: Types::anything()));

            $this->assertSupertypeStrict(
                $arrType1,
                new LooseStructuredArrayType([
                    Types::array(), Types::string(), Types::class(B::class),
                ], extraKeysType: Types::string()));

            $this->assertIncomparable(
                $arrType1,
                new LooseStructuredArrayType([
                    Types::array(), Types::string(),
                ], extraKeysType: Types::class(A::class)));
            
            $this->assertIncomparable(
                $arrType1,
                new LooseStructuredArrayType([
                    Types::int(), Types::string(), Types::class(B::class)
                ], extraKeysType: Types::anything()));
        }
    }
}

namespace PhpPlus\Core\Tests\Types\LooseStructuredArrayTypeTest
{
    class A { }
    class B extends A { }
}
