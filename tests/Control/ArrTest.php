<?php

namespace PhpPlus\Core\Tests\Control;

use PhpPlus\Core\Control\Arr;
use PhpPlus\Core\Tests\TestCase;
use PhpPlus\Core\Types\Type;

use Error;

/**
 * Tests for the Arr class.
 * @see Arr
 */
class ArrTest extends TestCase
{
    /**
     * Tests the Arr::zip() method.
     * @see Arr::zip()
     */
    public function testZip()
    {
        // Empty zip calls should just return an empty array.
        $this->assertSame([], Arr::zip());

        // Nonempty zip calls should zip the arrays passed in, cutting off arrays that are longer
        // than the shortest array passed in.
        $this->assertSame(
            Arr::zip(
                ['a' => 1, 'b' => 4, 'c' => 9, 'd' => 16, 'e' => null],
                [1, 2, 3],
                ['C' => 1, 8, 'n' => 27, 'YY' => 64, '%' => 'EEEEE']),
            [
                [1, 1, 1],
                [4, 2, 8],
                [9, 3, 27]
            ]);
    }

    /**
     * Tests the {@see Arr::typeCheck} function when a value is returned (no exception throw).
     */
    public function testTypeCheck_return()
    {
        $this->assertTrue(Arr::typeCheck([4, 5, 1, 0, 12], Type::int()));
        $this->assertFalse(Arr::typeCheck([1, 2, 3, 4, 5.0], Type::int()));
        $this->assertTrue(Arr::typeCheck(['3', tmpfile(), 444], Type::anything()));
        $this->assertFalse(Arr::typeCheck([null, null, null, 0], Type::nothing()));
    }

    /**
     * Tests the {@see Arr::typeCheck} function when exceptions are thrown on failure.
     */
    public function testTypeCheck_throw()
    {
        $this->expectException(Error::class);

        $this->assertTrue(Arr::typeCheck(['g', 'h', 'i'], Type::string(), throw: true));
        $this->assertTrue(Arr::typeCheck([null, null], Type::null(), throw: true));
        Arr::typeCheck([5, 6], Type::float(), throw: true); // Should fail
    }
}
