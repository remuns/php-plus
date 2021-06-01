<?php

namespace PhpPlus\Core\Types;

use PhpPlus\Core\Traits\StaticClassTrait;
use PhpPlus\Core\Traits\StaticConstructibleTrait;

final class Types
{
    use StaticClassTrait;
    use StaticConstructibleTrait;

    private static Type $meta;

    protected static function __initStatic(): void
    {
        self::$meta = new ClassType(Type::class);
    }

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
    /// ^FactoryMethods.Array
    ///////////////////////////////////////////////////////////////////////////
    /**
     * Returns the "array" type.
     * @return BaseArrayType
     */
    public static final function array(): BaseArrayType { return BaseArrayType::value(); }
    ///////////////////////////////////////////////////////////////////////////
    /// $FactoryMethods.Array
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
     * Gets a type representing the {@see Type} class.
     * @return Type
     */
    public static function meta(): Type { return self::$meta; }
} Types::__constructStatic();
