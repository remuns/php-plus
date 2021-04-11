<?php

namespace PhpPlus\Core\Tests\Traits
{
    use PhpPlus\Core\Exceptions\InvalidOperationException;
    use PhpPlus\Core\Tests\TestCase;
    use PhpPlus\Core\Tests\Traits\WellDefinedTest\C1;
    use PhpPlus\Core\Tests\Traits\WellDefinedTest\C2;
    use PhpPlus\Core\Tests\Traits\WellDefinedTest\D1;
    use PhpPlus\Core\Tests\Traits\WellDefinedTest\D2;

    class WellDefinedTest extends TestCase
    {
        /**
         * Tests the WellDefinedSelf trait.
         * @see WellDefinedSelf
         */
        public function testWellDefinedSelf()
        {
            $this->expectException(InvalidOperationException::class);

            // This should work, as the WellDefinedSelf trait does not enforce its restriction
            // on child classes
            $c2 = new C2;
            $c2->a = 1;
            $this->assertEquals(1, $c2->a);

            $c1 = new C1;
            $c1->a = 1; // Should throw an exception
        }

        /**
         * Tests the WellDefinedStatic trait by attempting a set operation on the class in which
         * the trait was used.
         * 
         * @see WellDefinedStatic
         */
        public function testWellDefinedStatic_parent()
        {
            $this->expectException(InvalidOperationException::class);
            $d1 = new D1;
            $d1->a = 1;
        }

        /**
         * Tests the WellDefinedStatic trait by attempting a set operation on a descendant of the
         * class in which the trait was used.
         * 
         * @see WellDefinedStatic
         */
        public function testWellDefinedStatic_child()
        {
            $this->expectException(InvalidOperationException::class);
            $d2 = new D2;
            $d2->a = 2;
        }
    }
}

namespace PhpPlus\Core\Tests\Traits\WellDefinedTest
{
    use PhpPlus\Core\Traits\WellDefinedSelf;
    use PhpPlus\Core\Traits\WellDefinedStatic;

    class C1
    {
        use WellDefinedSelf;
    }

    class C2 extends C1
    {
        // Should be able to override this
        public function __set(string $name, $value)
        {
            $this->{$name} = $value;
        }
    }

    class D1
    {
        use WellDefinedStatic;
    }

    class D2 extends D1
    {

    }
}
