<?php

namespace PhpPlus\Core\Tests\Control
{
    use PhpPlus\Core\Control\Access\Access;
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
         * Tests the {@see Access::access()} method.
         */
        public function testAccess()
        {
            $o = new Test1;
            $this->assertSame(4, Access::access($o, 'class', 'a'));
            $this->assertSame(4, Access::access($o, ['add1',[3]]));
            $this->assertFalse(Access::access($o, 'boolVal'));
            $this->assertTrue(Access::access($o, ['getTest2',[]], 'boolVal'));
            $this->assertSame(3, Access::access($o, ['getTest2',[]], ['getVal',[]]));
            $this->assertSame(1, Access::access($o, 'class', 'c', [0]));
        }

        /**
         * Tests the {@see Access::accessNullable()} method.
         */
        public function testAccessNullable()
        {
            $o = new Test1;
            $this->assertSame(4, Access::accessNullable($o, 'class', 'a'));
            $this->assertFalse(Access::accessNullable($o, 'boolVal'));
            $this->assertTrue(Access::accessNullable($o, ['getTest2',[]], 'boolVal'));
            $this->assertSame(3, Access::accessNullable($o, ['getTest2',[]], ['getVal',[]]));
            $this->assertSame(1, Access::accessNullable($o, 'class', 'c', [0]));

            // The getNull() method call will return null, so the access will stop there
            $this->assertNull(Access::accessNullable($o, ['getNull',[]], [5], 'a'));
        }

        /**
         * Tests the {@see Access::accessAccessible()} method.
         */
        public function testAccessAccessible()
        {
            $o = new Test1;
            $this->assertSame(4, Access::accessAccessible($o, 'class', 'a'));
            $this->assertFalse(Access::accessAccessible($o, 'boolVal'));
            $this->assertTrue(Access::accessAccessible($o, ['getTest2',[]], 'boolVal'));
            $this->assertSame(3, Access::accessAccessible($o, ['getTest2',[]], ['getVal',[]]));
            $this->assertSame(1, Access::accessAccessible($o, 'class', 'c', [0]));

            // The getNull() method call will return null, so the access should stop and return
            // null when another access is attempted
            $this->assertNull(Access::accessAccessible($o, ['getNull',[]], 5, 'a'));

            // The access should stop and return null when an attempt is made to access an array
            // as if it were an object ($o->class->c)
            $this->assertNull(Access::accessAccessible($o, 'class', 'c', 'd'));

            // The access should stop and return null when an attempt is made to access a
            // non-array-accessible object with an array offset ($o->class)
            $this->assertNull(Access::accessAccessible($o, 'class', [1], [2]));

            // The access should stop and return null when an attempt is made to access a
            // method on an array ($o->class->c)
            $this->assertNull(
                Access::accessAccessible($o, 'class', 'c', ['notAMethod',[2, 3]], [5]));
        }

        /**
         * Tests the {@see Access::accessDefined()} method.
         */
        public function testAccessDefined()
        {
            $o = new Test1;
            $this->assertSame(4, Access::accessDefined($o, 'class', 'a'));
            $this->assertFalse(Access::accessDefined($o, 'boolVal'));
            $this->assertTrue(Access::accessDefined($o, ['getTest2',[]], 'boolVal'));
            $this->assertSame(3, Access::accessDefined($o, ['getTest2',[]], ['getVal',[]]));
            $this->assertSame(1, Access::accessDefined($o, 'class', 'c', [0]));

            // The getNull() method call will return null, so the access should stop and return
            // null when another access is attempted
            $this->assertNull(Access::accessDefined($o, ['getNull',[]], [5], 'a'));

            // The access should stop and return null when an attempt is made to access an array
            // as if it were an object ($o->class->c)
            $this->assertNull(Access::accessDefined($o, 'class', 'c', 'd'));

            // The access should stop and return null when an attempt is made to access a
            // non-array-accessible object with an array offset ($o->class)
            $this->assertNull(Access::accessDefined($o, 'class', [1], [2]));

            // The access should stop and return null when an attempt is made to access a
            // method on an array ($o->class->c)
            $this->assertNull(
                Access::accessDefined($o, 'class', 'c', ['notAMethod',[2, false]], [5]));

            // The access should stop and return null on attempts to access undefined properties
            // on an object ($o)
            $this->assertNull(Access::accessDefined($o, 'class2'));

            // The access should stop and return null on attempts to access undefined methods
            // on an object ($o)
            $this->assertNull(Access::accessDefined($o, ['notAMethod',[4, 5, null]], 'h'));

            // The access should stop and return null on attempts to access undefined offsets
            // on an array ($o->class->c)
            $this->assertNull(Access::accessDefined($o, 'class', 'c', [3], 'g'));
        }
    }
}

namespace PhpPlus\Core\Tests\Control\AccessTest
{
    class Test1
    {
        public function get1(): int { return 1; }
        public function getTest2(): Test2 { return new Test2; }

        public function add1($i) { return $i + 1; }

        public function getNull() { return null; }

        public function returnSum(int ...$args)
        { 
            return array_reduce($args, fn($a, $b) => $a + $b);
        }

        public bool $boolVal = false;
        public \stdClass $class;

        public function __construct()
        {
            $this->class = (object)['a' => 4, 'b' => 5, 'c' => [1, 2]];
        }
    }

    class Test2
    {
        public bool $boolVal = true;
        public function getVal(): int { return 3; }
    }
}
