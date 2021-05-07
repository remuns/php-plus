<?php

namespace PhpPlus\Core\Tests\Types;

use PhpPlus\Core\Tests\TestCase;
use PhpPlus\Core\Types\Type;
use PHPUnit\Framework\AssertionFailedError;

/**
 * A base class for test cases testing the PhpPlus\Core\Types namespace.
 */
abstract class TypeTestCase extends TestCase
{
    /**
     * Asserts that the value passed in is an instance of the type passed in.
     * 
     * @param Type  $expectedType   The type to test containment for.
     * @param mixed $value          The value to test.
     * 
     * @throws AssertionFailedError The assertion failed.
     */
    public final function assertHas(Type $expectedType, mixed $value)
    {
        $this->assertTrue(
            $expectedType->has($value),
            "the value passed in was not an instance of {$expectedType}");
    }

    /**
     * Asserts that the value passed in is not an instance of the type passed in.
     * 
     * @param Type  $notExpectedType    The type to test containment for.
     * @param mixed $value              The value to test.
     * 
     * @throws AssertionFailedError The assertion failed.
     */
    public final function assertNotHas(Type $notExpectedType, mixed $value)
    {
        $this->assertFalse(
            $notExpectedType->has($value),
            "The value passed in was an instance of {$notExpectedType}.");
    }

    /**
     * Asserts that the first type passed in is a strict supertype of the second.
     * 
     * @param Type $expectedParent  The type that is expected to be a strict supertype of the
     *                              second type.
     * @param Type $actualChild     The type to check for the expected type comparison
     *                              relationship.
     * 
     * @throws AssertionFailedError The assertion failed.
     */
    public final function assertSupertypeStrict(Type $expectedParent, Type $actualChild)
    {
        $comparison = $expectedParent->compare($actualChild);
        $this->assertNotNull(
            $comparison,
            "Expected a strict subtype of {$expectedParent}, " .
                "got incomparable type {$actualChild}.");
        $this->assertGreaterThan(
            0,
            $comparison,
            "Expected a strict subtype of {$expectedParent}, " .
                "got supertype {$actualChild}. comparison {$comparison}");
    }

    /**
     * Asserts that the first type passed in is a strict subtype of the second.
     * 
     * @param Type $expectedChild   The type that is expected to be a strict subtype of the
     *                              second type.
     * @param Type $actualParent    The type to check for the expected type comparison
     *                              relationship.
     * 
     * @throws AssertionFailedError The assertion failed.
     */
    public final function assertSubtypeStrict(Type $expectedChild, Type $actualParent)
    {
        $comparison = $expectedChild->compare($actualParent);
        $this->assertNotNull(
            $comparison,
            "Expected a strict supertype of {$expectedChild}, " .
                "got incomparable type {$actualParent}.");
        $this->assertLessThan(
            0,
            $comparison,
            "Expected a strict supertype of {$expectedChild}, " .
                "got subtype {$actualParent}.");
    }

    /**
     * Asserts that the first type passed in is the same as the second using comparisons.
     * 
     * @param Type $expected        The expected type.
     * @param Type $actual          The actual type.
    * 
     * @throws AssertionFailedError The assertion failed.
     */
    public final function assertSameType(Type $expected, Type $actual)
    {
        $comparison = $expected->compare($actual);
        $this->assertNotNull(
            $comparison,
            "Expected type {$expected} did not equal actual type {$actual}.");
        $this->assertSame(
            0,
            $comparison,
            "Expected type {$expected} did not equal actual type {$actual}.");
    }

    /**
     * Asserts that the two types passed in are incomparable.
     * 
     * @param Type $type            The type that is expected to not compare with the second type.
     * @param Type $actualParent    The type to check for the expected type comparison
     *                              relationship.
     * 
     * @throws AssertionFailedError The assertion failed.
     */
    public final function assertIncomparable(Type $type, Type $notComparing)
    {
        $comparison = $type->compare($notComparing);
        $this->assertNull(
            $comparison,
            "Type {$type} was not incomparable with type {$notComparing}.");
    }
}
