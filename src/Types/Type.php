<?php

namespace PhpPlus\Core\Types;

use PhpPlus\Core\Traits\WellDefinedStatic;

/**
 * A class representing a PHP type.
 */
abstract class Type
{
    use WellDefinedStatic;

    ///////////////////////////////////////////////////////////////////////////
    /// ^StaticProperties
    ///////////////////////////////////////////////////////////////////////////
    public const INTERSECTION_INDICATOR = -4;
    public const UNION_INDICATOR = -2;
    public const ANYTHING_INDICATOR = -1;
    public const NOTHING_INDICATOR = 0;
    public const ARRAY_INDICATOR = 1;
    public const BOOLEAN_INDICATOR = 2;
    public const FLOAT_INDICATOR = 4;
    public const INTEGER_INDICATOR = 8;
    public const NULL_INDICATOR = 16;
    public const OBJECT_INDICATOR = 32;
    public const RESOURCE_INDICATOR = 64;
    public const STRING_INDICATOR = 128;

    /**
     * A type describing the type of this class.
     */
    private static self $meta;
    ///////////////////////////////////////////////////////////////////////////
    /// $StaticProperties
    ///////////////////////////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////////////////////////
    /// ^Constructors
    ///////////////////////////////////////////////////////////////////////////
    protected function __construct() { }
    ///////////////////////////////////////////////////////////////////////////
    /// $Constructors
    ///////////////////////////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////////////////////////
    /// ^Comparisons
    ///////////////////////////////////////////////////////////////////////////
    /**
     * Determines whether this type is equal to or a strict subtype of the type passed in.
     * @param self $other The other type to compare with.
     * @return bool
     */
    public function isSubtypeOf(self $other): bool
    {
        $comparison = $this->compare($other);
        return $comparison !== null && $comparison <= 0;
    }

    /**
     * Determines whether this type is equal to or a strict supertype of the type passed in.
     * @param self $other The other type to compare with.
     * @return bool
     */
    public function isSupertypeOf(self $other): bool
    {
        $comparison = $this->compare($other);
        return $comparison !== null && $comparison >= 0;
    }

    /**
     * Determines whether this type is a strict subtype of the type passed in.
     * @param self $other The other type to compare with.
     * @return bool
     */
    public function isStrictSubtypeOf(self $other): bool
    {
        $comparison = $this->compare($other);
        return $comparison !== null && $comparison < 0; 
    }

    /**
     * Determines whether this type is a strict supertype of the type passed in.
     * @param self $other The other type to compare with.
     * @return bool
     */
    public function isStrictSupertypeOf(self $other): bool
    {
        $comparison = $this->compare($other);
        return $comparison !== null && $comparison > 0; 
    }

    /**
     * Compares the two types passed in.
     * @param self $other The other type to compare with.
     * @return int|null An integer describing the subtype relationship of the two types, or null
     *                  if the two types are not comparable.
     */
    public abstract function compare(self $other): ?int;
    ///////////////////////////////////////////////////////////////////////////
    /// $Comparisons
    ///////////////////////////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////////////////////////
    /// ^TypeChecking
    ///////////////////////////////////////////////////////////////////////////
    /**
     * Gets an indicator describing what kind of type this instance represents.
     */
    public abstract function typeIndicator(): int;

    /**
     * Type-checks the item passed in, throwing an error if the check fails.
     * @throws PhpPlusTypeError
     */
    public final function check($item): void
    {
        if (!$this->has($item)) {
            throw new PhpPlusTypeError("expected a value of type {$this}");
        }
    }

    /**
     * Determines whether or not the item passed in is an instance of this type.
     * @param mixed $item The item to type-check.
     * @return bool
     */
    public abstract function has($item): bool;
    ///////////////////////////////////////////////////////////////////////////
    /// $TypeChecking
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
    /// ^OtherMethods
    ///////////////////////////////////////////////////////////////////////////
    /**
     * Returns the string representation of this type.
     * @return string
     */
    public abstract function __toString(): string;

    /**
     * Handles the trivial compare cases after all interesting cases have been resolved.
     * This method should be called on the $other parameter of a compare method once all cases
     * other than "nothing", "anything" and non-comparable cases have been handled.
     * @internal
     * @return int|null
     */
    public abstract function handleTrivialCompareCases(): ?int;
    ///////////////////////////////////////////////////////////////////////////
    /// $OtherMethods
    ///////////////////////////////////////////////////////////////////////////
}
