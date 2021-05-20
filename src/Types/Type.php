<?php

namespace PhpPlus\Core\Types;

use PhpPlus\Core\Traits\StaticConstructibleTrait;
use PhpPlus\Core\Traits\WellDefinedStatic;

/**
 * A class representing a PHP type.
 */
abstract class Type
{
    use StaticConstructibleTrait, WellDefinedStatic;

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

    protected static function __initStatic(): void {
        self::$meta = new ClassType(self::class);
    }
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
    /// ^FactoryMethods
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
    /// ^FactoryMethods.Trivial
    ///////////////////////////////////////////////////////////////////////////
    /**
     * Returns the "anything" type at the root of the type hierarchy.
     * @return AnythingType
     */
    public static final function anything(): AnythingType { return AnythingType::value(); }

    /**
     * Returns the "nothing" empty type at the bottom of the type hierarchy.
     * @return NothingType
     */
    public static final function nothing(): NothingType { return NothingType::value(); }
    ///////////////////////////////////////////////////////////////////////////
    /// $FactoryMethods.Trivial
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
    /// ^FactoryMethods.SimplePrimitive
    ///////////////////////////////////////////////////////////////////////////
    /**
     * Returns the "double" (float) type.
     * @return FloatType
     */
    public static final function float(): FloatType { return FloatType::value(); }

    /**
     * Returns the "integer" type.
     * @return IntType
     */
    public static final function int(): IntType { return IntType::value(); }

    /**
     * Returns the "NULL" type.
     * @return NullType
     */
    public static final function null(): NullType { return NullType::value(); }

    /**
     * Returns the "string" type.
     * @return StringType
     */
    public static final function string(): StringType { return StringType::value(); }
    ///////////////////////////////////////////////////////////////////////////
    /// $FactoryMethods.SimplePrimitive
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
    /// ^FactoryMethods.Boolean
    ///////////////////////////////////////////////////////////////////////////
    /**
     * Returns the "boolean" type.
     * @return BaseBoolType
     */
    public static final function bool(): BaseBoolType { return BaseBoolType::value(); }

    /**
     * Returns the "true" type, representing only the "true" boolean value.
     * @return TrueType
     */
    public static final function true(): TrueType { return TrueType::value(); }

    /**
     * Returns the "false" type, representing only the "false" boolean value.
     * @return FalseType
     */
    public static final function false(): FalseType { return FalseType::value(); }
    ///////////////////////////////////////////////////////////////////////////
    /// $FactoryMethods.Boolean
    ///////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////////////////////////////////
    /// ^FactoryMethods.Object
    ///////////////////////////////////////////////////////////////////////////
    /**
     * Returns the "object" type at the root of the class hierarchy.
     * @return BaseObjectType
     */
    public static final function object(): BaseObjectType { return BaseObjectType::value(); }

    /**
     * Returns a class type with the given fully qualified name.
     * @param string $name The fully qualified name of the class to create a type for.
     * @return ClassType
     */
    public static final function class(string $name): ClassType { return new ClassType($name); }

    /**
     * Returns a class type with the given fully qualified name, throwing an exception if the
     * name does not refer to an existing class.
     * @param string $name The fully qualified name of the class to create a type for.
     * @return ClassType
     * @throws ClassNotFoundException The name passed in did not refer to an existing class.
     */
    public static final function definedClass(string $name): ClassType
    {
        if (!class_exists($name)) {
            throw new ClassNotFoundException("class with name $name does not exist");
        }
        return new ClassType($name);
    }
    ///////////////////////////////////////////////////////////////////////////
    /// $FactoryMethods.Object
    ///////////////////////////////////////////////////////////////////////////
 
    ///////////////////////////////////////////////////////////////////////////
    /// $FactoryMethods
    ///////////////////////////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////////////////////////
    /// ^OtherMethods
    ///////////////////////////////////////////////////////////////////////////
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
