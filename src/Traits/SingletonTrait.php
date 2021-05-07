<?php

namespace PhpPlus\Core\Traits;

/**
 * A trait for classes that should only ever have a single instance.
 * The instance can be accessed via calls to the value() method.
 * 
 * This trait will create a __constructStatic method that should be called immediately after the
 * class definition in which the trait is used.
 * 
 * It is also recommended that singleton classes be made final (although this cannot be enforced).
 * The trait will extend the {@see InternalDefaultConstructibleTrait} trait so that the private
 * parameterless constructor provided will be marked as final.
 */
trait SingletonTrait
{
    use InternalDefaultConstructibleTrait, StaticConstructibleTrait;
    
    private static $value;

    /**
     * Sets up the class before interaction.
     * This method should be called immediately after the class definition.
     */
    public static final function __initStatic(): void
    {
        static::$value = new static;
    }

    /**
     * Gets the single instance of this class.
     * @return static
     */
    public static final function value(): static { return static::$value; }
}
