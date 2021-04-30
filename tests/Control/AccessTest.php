<?php

namespace PhpPlus\Core\Tests\Control
{
    use PhpPlus\Core\Control\Access;
    use PhpPlus\Core\Tests\Control\AccessTest\Test1;
    use PhpPlus\Core\Tests\TestCase;

    /**
     * Tests for the Access static class.
     * 
     * @see Access
     */
    class AccessTest extends TestCase
    {
        /**
         * Tests the Access::accessOnce() method.
         * @see Access::accessOnce()
         */
        public function testAccessOnce()
        {
            $o = new Test1;
            $this->assertFalse(Access::accessOnce($o, 'boolVal'));
            $this->assertEquals(1, Access::accessOnce($o, ['get1']));
            $this->assertEquals(10, Access::accessOnce($o, ['returnSum', 1, 2, 3, 4]));
        }

        /**
         * Tests the Access::access() method.
         * @see Access::access()
         */
        public function testAccess()
        {
            $o = new Test1;
            $this->assertSame(4, Access::access($o, 'class', 'a'));
            $this->assertFalse(Access::access($o, 'boolVal'));
            $this->assertTrue(Access::access($o, ['getTest2'], 'boolVal'));
            $this->assertSame(3, Access::access($o, ['getTest2'], ['getVal']));
        }

        /**
         * Tests the Access::accessArray() method.
         * @see Access::accessArray()
         */
        public function testAccessArray()
        {
            $o = new Test1;
            $this->assertSame(4, Access::accessArray($o, ['class', 'a']));
            $this->assertFalse(Access::accessArray($o, ['boolVal']));
            $this->assertTrue(Access::accessArray($o, [['getTest2'], 'boolVal']));
            $this->assertSame(3, Access::accessArray($o, [['getTest2'], ['getVal']]));
        }

        /**
         * Tests the {@see Access::arrayNullable()} method.
         */
        public function testArrayNullable()
        {
            // Create a test array to access
            $a = [0, 1, 'f' => [2, 3]];

            $this->assertSame(0, Access::arrayNullable($a, 0));
            $this->assertSame(1, Access::arrayNullable($a, 1));
            $this->assertSame(3, Access::arrayNullable($a, 'f', 1));

            $this->assertNull(Access::arrayNullable($a, 'g', 'h', 4, 'i'));
        }
    }
}

namespace PhpPlus\Core\Tests\Control\AccessTest
{
    class Test1
    {
        public function get1(): int { return 1; }
        public function getTest2(): Test2 { return new Test2; }

        public function returnSum(int ...$args)
        { 
            return array_reduce($args, fn($a, $b) => $a + $b);
        }

        public bool $boolVal = false;
        public \stdClass $class;

        public function __construct()
        {
            $this->class = (object)['a' => 4, 'b' => 5];
        }
    }

    class Test2
    {
        public bool $boolVal = true;
        public function getVal(): int { return 3; }
    }
}
