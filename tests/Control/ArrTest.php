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

    /**
     * Tests the {@see Arr::typeCheckLooseStructure} method with no extra arguments allowed when
     * a value is returned on failure.
     */
    public function testTypeCheckLooseStructure_noExtras_return()
    {
        $intType = Type::int();
        $strType = Type::string();
        $floatType = Type::float();

        $this->assertTrue(
            Arr::typeCheckLooseStructure(
                [ 1, 2, 'f', 'g', 6.7 ],
                [ $intType, $intType, $strType, $strType, $floatType ]));

        $this->assertTrue(
            Arr::typeCheckLooseStructure(
                [ 1, 2 => 'f', 1 => 4.5 ],
                [ $intType, $floatType, $strType ]));

        $this->assertFalse(
            Arr::typeCheckLooseStructure(
                [ 1, 2, 'f' ], [ $intType, $intType, $intType ]));

        // Will fail because there is an extra argument
        $this->assertFalse(
            Arr::typeCheckLooseStructure(
                [ 1, 2, 3, 'f' ], [ $intType, $intType, $intType ]));
    }

    /**
     * Tests the {@see Arr::typeCheckLooseStructure} method with no extra arguments allowed when
     * an error is thrown on failure.
     */
    public function testTypeCheckLooseStructure_noExtras_throw()
    {
        $this->expectException(Error::class);

        $intType = Type::int();
        $strType = Type::string();
        $floatType = Type::float();

        $this->assertTrue(
            Arr::typeCheckLooseStructure(
                [ 1, 2, 'f', 'g', 6.7 ],
                [ $intType, $intType, $strType, $strType, $floatType ],
                throw: true));

        $this->assertTrue(
            Arr::typeCheckLooseStructure(
                [ 1, 2 => 'f', 1 => 4.5 ],
                [ $intType, $floatType, $strType ],
                throw: true));

        // Should fail
        Arr::typeCheckLooseStructure(
            [ 1, 2, 'f' ], [ $intType, $intType, $intType ], throw: true);
    }

    /**
     * Tests the {@see Arr::typeCheckLooseStructure} method with no extra arguments allowed when
     * a value is returned on failure.
     */
    public function testTypeCheckLooseStructure_extras_return()
    {
        $intType = Type::int();
        $strType = Type::string();
        $floatType = Type::float();

        $this->assertTrue(
            Arr::typeCheckLooseStructure(
                [ 1, 2, 'f', 'g', 6.7, 8.3, 'y' ],
                [ $intType, $intType, $strType, $strType, $floatType ],
                allowAdditionalValues: true));

        $this->assertTrue(
            Arr::typeCheckLooseStructure(
                [ 1, 2 => 'f', 5 => null, 1 => 4.5 ],
                [ $intType, $floatType, $strType, ],
                allowAdditionalValues: true));

        $this->assertFalse(
            Arr::typeCheckLooseStructure(
                [ 1, 2, 'f', 'g' ],
                [ $intType, $intType, $intType ],
                allowAdditionalValues: true));
    }

    /**
     * Tests the {@see Arr::typeCheckLooseStructure} method with no extra arguments allowed when
     * an error is thrown on failure.
     */
    public function testTypeCheckLooseStructure_extras_throw()
    {
        $this->expectException(Error::class);

        $intType = Type::int();
        $strType = Type::string();
        $floatType = Type::float();

        $this->assertTrue(
            Arr::typeCheckLooseStructure(
                [ 1, 2, 'f', 'g', 6.7, 8.3, 'y' ],
                [ $intType, $intType, $strType, $strType, $floatType ],
                allowAdditionalValues: true,
                throw: true));

        $this->assertTrue(
            Arr::typeCheckLooseStructure(
                [ 1, 2 => 'f', 5 => null, 1 => 4.5 ],
                [ $intType, $floatType, $strType, ],
                allowAdditionalValues: true,
                throw: true));

        // Should fail
        Arr::typeCheckLooseStructure(
            [ 1, 2, 'f', 'g' ],
            [ $intType, $intType, $intType ],
            allowAdditionalValues: true,
            throw: true);
    }

    /**
     * Tests the {@see Arr::typeCheckStrictStructure} method with no extra arguments allowed when
     * a value is returned on failure.
     */
    public function testTypeCheckStrictStructure_noExtras_return()
    {
        $intType = Type::int();
        $strType = Type::string();
        $floatType = Type::float();

        $this->assertTrue(
            Arr::typeCheckStrictStructure(
                [ 1, 2, 'f', 'g', 6.7 ],
                [ $intType, $intType, $strType, $strType, $floatType ]));

        $this->assertTrue(
            Arr::typeCheckStrictStructure(
                [ 1, 2 => 'f', 1 => 4.5 ],
                [ $intType, 2 => $strType, 1 => $floatType ]));

        $this->assertFalse(
            Arr::typeCheckStrictStructure(
                [ 1, 2, 'f' ], [ $intType, $intType, $intType ]));

        // Will fail because there is an extra argument
        $this->assertFalse(
            Arr::typeCheckStrictStructure(
                [ 1, 2, 3, 'f' ], [ $intType, $intType, $intType ]));

        // Will fail because is strict structure
        $this->assertFalse(
            Arr::typeCheckStrictStructure(
                [ 1, 2, 3, 'f' ], [ $intType, $intType, 3 => $strType, 2 => $intType ]));
    }

    /**
     * Tests the {@see Arr::typeCheckStrictStructure} method with no extra arguments allowed when
     * a value is returned on failure.
     */
    public function testTypeCheckStrictStructure_noExtras_throw()
    {
        $this->expectException(Error::class);

        $intType = Type::int();
        $strType = Type::string();
        $floatType = Type::float();

        $this->assertTrue(
            Arr::typeCheckStrictStructure(
                [ 1, 2, 'f', 'g', 6.7 ],
                [ $intType, $intType, $strType, $strType, $floatType ],
            throw: true));

        $this->assertTrue(
            Arr::typeCheckStrictStructure(
                [ 1, 2 => 'f', 1 => 4.5 ],
                [ $intType, 2 => $strType, 1 => $floatType ],
                throw: true));

        // Should fail
        Arr::typeCheckStrictStructure(
            [ 1, 2, 'f' ],
            [ $intType, $intType, $intType ],
            throw: true);
    }

    /**
     * Tests the {@see Arr::typeCheckStrictStructure} method with extra arguments allowed when
     * a value is returned on failure.
     */
    public function testTypeCheckStrictStructure_extras_return()
    {
        $intType = Type::int();
        $strType = Type::string();
        $floatType = Type::float();

        $this->assertTrue(
            Arr::typeCheckStrictStructure(
                [ 1, 2, 'f', 'g', 6.7 ],
                [ $intType, $intType, $strType, $strType, $floatType ],
            allowAdditionalValues: true));

        $this->assertTrue(
            Arr::typeCheckStrictStructure(
                [ 1, 2 => 'f', 1 => 4.5 ],
                [ $intType, 2 => $strType, 1 => $floatType ],
                allowAdditionalValues: true));

        $this->assertFalse(
            Arr::typeCheckStrictStructure(
                [ 1, 2, 'f' ], [ $intType, $intType, $intType ],
                allowAdditionalValues: true));

        $this->assertTrue(
            Arr::typeCheckStrictStructure(
                [ 1, 2, 3, 'f' ], [ $intType, $intType, $intType ],
                allowAdditionalValues: true));

        // Will fail because is strict structure
        $this->assertFalse(
            Arr::typeCheckStrictStructure(
                [ 1, 2, 3, 'f' ], [ $intType, $intType, 3 => $strType ],
                allowAdditionalValues: true));
    }

    /**
     * Tests the {@see Arr::typeCheckStrictStructure} method with extra arguments allowed when
     * an error is thrown on failure.
     */
    public function testTypeCheckStrictStructure_extras_throw()
    {
        $this->expectException(Error::class);

        $intType = Type::int();
        $strType = Type::string();
        $floatType = Type::float();
        $nullType = Type::null();

        $this->assertTrue(
            Arr::typeCheckStrictStructure(
                [ 1, 2, 'f', 'g', 6.7, 8.3, 'y' ],
                [ $intType, $intType, $strType, $strType, $floatType ],
                allowAdditionalValues: true,
                throw: true));

        $this->assertTrue(
            Arr::typeCheckStrictStructure(
                [ 1, 2 => 'f', 5 => null, 1 => 4.5 ],
                [ $intType, 2 => $strType, 5 => $nullType, ],
                allowAdditionalValues: true,
                throw: true));

        // Should fail since the string argument is out of place
        Arr::typeCheckStrictStructure(
            [ 1, 2, 'f', 'g' ],
            [ $intType, $intType, 3 => $strType ],
            allowAdditionalValues: true,
            throw: true);
    }
}
