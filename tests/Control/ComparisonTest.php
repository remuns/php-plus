<?php

namespace PhpPlus\Core\Tests\Control;

use PhpPlus\Core\Control\Comparison;
use PhpPlus\Core\Tests\TestCase;

class ComparisonTest extends TestCase
{
    public function testIsLess()
    {
        $this->assertTrue(Comparison::less()->isLess);
        $this->assertFalse(Comparison::equal()->isLess);
        $this->assertFalse(Comparison::greater()->isLess);
    }

    public function testIsEqual()
    {
        $this->assertFalse(Comparison::less()->isEqual);
        $this->assertTrue(Comparison::equal()->isEqual);
        $this->assertFalse(Comparison::greater()->isEqual);
    }

    public function testIsGreater()
    {
        $this->assertFalse(Comparison::less()->isGreater);
        $this->assertFalse(Comparison::equal()->isGreater);
        $this->assertTrue(Comparison::greater()->isGreater);
    }

    public function testIsLessOrEqual()
    {
        $this->assertTrue(Comparison::less()->isLessOrEqual);
        $this->assertTrue(Comparison::equal()->isLessOrEqual);
        $this->assertFalse(Comparison::greater()->isLessOrEqual);
    }

    public function testIsNotEqual()
    {
        $this->assertTrue(Comparison::less()->isNotEqual);
        $this->assertFalse(Comparison::equal()->isNotEqual);
        $this->assertTrue(Comparison::greater()->isNotEqual);
    }

    public function testIsGreaterOrEqual()
    {
        $this->assertFalse(Comparison::less()->isGreaterOrEqual);
        $this->assertTrue(Comparison::equal()->isGreaterOrEqual);
        $this->assertTrue(Comparison::greater()->isGreaterOrEqual);
    }

    public function testSpaceship()
    {
        $this->assertEquals(Comparison::less(), Comparison::spaceship(4, 5));
        $this->assertEquals(Comparison::equal(), Comparison::spaceship(0.44, 0.44));
        $this->assertEquals(Comparison::greater(), Comparison::spaceship(-1, -1000));
    }

    public function testFromSpaceship()
    {
        $this->assertEquals(Comparison::less(), Comparison::fromSpaceship(-44));
        $this->assertEquals(Comparison::equal(), Comparison::fromSpaceship(0));
        $this->assertEquals(Comparison::greater(), Comparison::fromSpaceship(88));
    }

    public function testFromNullableSpaceship()
    {
        $this->assertEquals(Comparison::less(), Comparison::fromNullableSpaceship(-44));
        $this->assertEquals(Comparison::equal(), Comparison::fromNullableSpaceship(0));
        $this->assertEquals(Comparison::greater(), Comparison::fromNullableSpaceship(88));
        $this->assertNull(Comparison::fromNullableSpaceship(null));
    }

    public function testCompare_less()
    {
        $this->assertTrue(Comparison::less()->compare(0, 1));
        $this->assertFalse(Comparison::less()->compare(0, 0));
        $this->assertFalse(Comparison::less()->compare(1, 0));
    }

    public function testCompare_equal()
    {
        $this->assertFalse(Comparison::equal()->compare(0, 1));
        $this->assertTrue(Comparison::equal()->compare(0, 0));
        $this->assertFalse(Comparison::equal()->compare(1, 0));
    }

    public function testCompare_greater()
    {
        $this->assertFalse(Comparison::greater()->compare(0, 1));
        $this->assertFalse(Comparison::greater()->compare(0, 0));
        $this->assertTrue(Comparison::greater()->compare(1, 0));
    }

    public function testChoose()
    {
        $this->assertSame(-1, Comparison::less()->choose(-1, 0, 1));
        $this->assertSame(0, Comparison::equal()->choose(-1, 0, 1));
        $this->assertSame(1, Comparison::greater()->choose(-1, 0, 1));
    }

    public function testMap()
    {
        $this->assertSame(-1, Comparison::less()->map(fn() => -1, fn() => 0, fn() => 1));
        $this->assertSame(0, Comparison::equal()->map(fn() => -1, fn() => 0, fn() => 1));
        $this->assertSame(1, Comparison::greater()->map(fn() => -1, fn() => 0, fn() => 1));
    }
}
