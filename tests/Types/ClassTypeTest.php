<?php

namespace PhpPlus\Core\Tests\Types
{
    use PhpPlus\Core\Tests\Types\ClassTypeTest\{A, B, C};
    use PhpPlus\Core\Types\ClassType;

    class ClassTypeTest extends TypeTestCase
    {
        /**
         * Tests whether or not class types have given values
         */
        public function testOwnership()
        {
            $a = new A(2);
            $b = new B(4);
            $c = new C;
            $aType = new ClassType(A::class);
            $bType = new ClassType(B::class);
            $cType = new ClassType(C::class);

            // Test the type of A
            $this->assertHas($aType, $a);
            $this->assertHas($aType, $b);
            $this->assertNotHas($aType, $c);
            $this->assertNotHas($aType, A::class);
            $this->assertNotHas($aType, null);

            // Test the type of B
            $this->assertNotHas($bType, $a);
            $this->assertHas($bType, $b);
            $this->assertNotHas($bType, $c);
            $this->assertNotHas($bType, B::class);
            $this->assertNotHas($bType, 1);

            // Test the type of C
            $this->assertNotHas($cType, $a);
            $this->assertNotHas($cType, $b);
            $this->assertHas($cType, $c);
            $this->assertNotHas($cType, C::class);
            $this->assertNotHas($cType, 1.4);
        }
    }
}

namespace PhpPlus\Core\Tests\Types\ClassTypeTest
{
    class A
    {
        public static int $staticI = 1;
        public function __construct(public int $i) { }
        public function f() { return $this->i; }
        public static function staticF() { return static::$staticI; }
    }

    class B extends A
    {
        public function g(): int { return $this->i + 1; }
    }

    class C { }
}
