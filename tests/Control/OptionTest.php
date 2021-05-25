<?php

namespace PhpPlus\Core\Tests\Control
{

    use PhpPlus\Core\Control\Option;
    use PhpPlus\Core\Exceptions\InvalidOperationException;
    use PhpPlus\Core\Tests\Control\OptionTest\TestClass;
    use PhpPlus\Core\Tests\TestCase;

    use Error;
    use stdClass;

    class OptionTest extends TestCase
    {
        ////////   Tests   ////////
        public function testSimpleConstruction()
        {
            $this->assertTrue(Option::none()->isNone);
            $this->assertTrue(Option::some(5)->isSome);
        }

        public function testHas()
        {
            // The method should use loose comparison
            $this->assertTrue(Option::some(5)->has(5));
            $this->assertTrue(Option::some(5)->has(5.0));
            $this->assertFalse(Option::some(5)->has(4.0));
            $this->assertNull(Option::none()->has(2));
        }

        public function testHasStrict()
        {
            // The method should use strict comparison
            $this->assertTrue(Option::some(5)->hasStrict(5));
            $this->assertFalse(Option::some(5)->hasStrict(5.0));
            $this->assertFalse(Option::some(5)->hasStrict(4));
            $this->assertNull(Option::none()->hasStrict(4));
        }

        public function testValue_success()
        {
            $this->assertSame(4, Option::some(4)->value);
        }

        public function testValue_error()
        {
            $this->expectException(InvalidOperationException::class);
            Option::none()->value; // Should fail with the expected exception
        }

        public function testValueOrNull()
        {
            $this->assertSame(4, Option::some(4)->nValue);
            $this->assertFalse(Option::some(false)->nValue);
            $this->assertNull(Option::none()->nValue);
        }

        public function testValueOrFalse()
        {
            $this->assertSame(4, Option::some(4)->fValue);
            $this->assertNull(Option::some(null)->fValue);
            $this->assertFalse(Option::none()->fValue);
        }

        public function testMap()
        {
            $squarer = fn ($x) => $x * $x;
            $this->assertHasStrict(Option::some(4)->map($squarer), 16);
            $this->assertIsNone(Option::none()->map($squarer));
        }

        public function testMapAll()
        {
            $squarer = fn ($x) => $x * $x;
            $cuber = fn ($x) => $x * $x * $x;

            $this->assertHasStrict(Option::some(2)->mapAll($squarer, $cuber), 64);
            $this->assertIsNone(Option::none()->mapAll($squarer, $cuber));
        }

        public function testAccess()
        {
            $obj = new stdClass;
            $obj->a = new TestClass;
            $obj->a->c = 2;

            $opt = Option::some($obj);
            $this->assertHasStrict($opt->access('a', ['add1',[3]]), 4);
            $this->assertHasStrict($opt->access('a', 'c'), 2);
            $this->assertIsNone(
                Option::none()->access('these', 'dont', ['matter',['in', 'this', 'case']]));
        }

        public function testApply_success()
        {
            $func = fn ($x, $y) => $x * $y;
            $this->assertHasStrict(Option::some($func)->apply(3, 4), 12);
            $this->assertIsNone(Option::none()->apply(1, 2, 3));
        }

        public function testApply_typeError()
        {
            $this->expectException(Error::class);
            Option::some(4)->apply(5);
        }

        public function testBind()
        {
            $func = fn ($x) => $x > 0 ? Option::some($x) : Option::none();
            $this->assertHasStrict(Option::some(2)->bind($func), 2);
            $this->assertIsNone(Option::some(-1)->bind($func));
            $this->assertIsNone(Option::none()->bind($func));
        }

        public function testCollapseNull()
        {
            $this->assertHasStrict(Option::some(4)->collapseNull(), 4);
            $this->assertIsNone(Option::some(null)->collapseNull());
            $this->assertIsNone(Option::none()->collapseNull());
        }

        public function testCollapseFalsy()
        {
            $this->assertHasStrict(Option::some(4)->collapseFalsy(), 4);
            
            $this->assertIsNone(Option::some(0)->collapseFalsy());
            $this->assertIsNone(Option::some(null)->collapseFalsy());
            $this->assertIsNone(Option::some(false)->collapseFalsy());

            $this->assertIsNone(Option::none()->collapseFalsy());
        }

        public function testAccessNullable()
        {
            $obj = new stdClass;
            $obj->a = new TestClass;
            $obj->b = 4;
            $obj->c = new stdClass;
            $obj->c->d = null;

            $opt = Option::some($obj);
            $this->assertHasStrict($opt->accessNullable('a', ['add1',[4]]), 5);
            $this->assertHasStrict($opt->accessNullable('b'), 4);

            // This will hit null and stop evaluating
            $this->assertIsNone($opt->accessNullable('c', 'd', 'e'));

            $this->assertIsNone(Option::none()->accessNullable('k'));
        }

        public function testAccessFalsy()
        {
            $obj = new stdClass;
            $obj->a = new TestClass;
            $obj->b = 4;
            $obj->c = new stdClass;
            $obj->c->d = 0;

            $opt = Option::some($obj);
            $this->assertHasStrict($opt->accessFalsy('a', ['add1',[4]]), 5);
            $this->assertHasStrict($opt->accessFalsy('b'), 4);

            // This will hit a false-equivalent value and stop evaluating
            $this->assertIsNone($opt->accessFalsy('c', 'd', 'e'));

            $this->assertIsNone(Option::none()->accessFalsy('k'));
        }

        public function testFromNullable()
        {
            $this->assertHasStrict(Option::fromNullable(4), 4);
            $this->assertHasStrict(Option::fromNullable(false), false);
            $this->assertIsNone(Option::fromNullable(null));
        }

        public function testFromFalsy()
        {
            $this->assertHasStrict(Option::fromFalsy(4), 4);
            $this->assertIsNone(Option::fromFalsy(0));
            $this->assertIsNone(Option::fromFalsy([]));
            $this->assertIsNone(Option::fromFalsy(false));
            $this->assertIsNone(Option::fromFalsy(null));
        }

        public function testFromNullableOption()
        {
            $this->assertHasStrict(Option::fromNullableOption(Option::some(40)), 40);
            $this->assertIsNone(Option::fromNullableOption(Option::none()));
            $this->assertIsNone(Option::fromNullableOption(null));
        }
        ///////////////////////////////////////

        ////////   Assertion Helpers   ////////
        protected function assertIsNone(Option $option, string $message = '')
        {
            $this->assertTrue($option->isNone, $message);
        }

        protected function assertHas(Option $option, mixed $item, string $message = '')
        {
            $this->assertTrue($option->has($item), $message);
        }

        protected function assertHasStrict(Option $option, mixed $item, string $message = '')
        {
            $this->assertTrue($option->hasStrict($item), $message);
        }

        protected function assertIsSome(Option $option, string $message = '')
        {
            $this->assertTrue($option->isSome, $message);
        }
        ///////////////////////////////////////
    }
}

namespace PhpPlus\Core\Tests\Control\OptionTest
{
    class TestClass
    {
        public function add1($x) { return $x + 1; }
    }
}
