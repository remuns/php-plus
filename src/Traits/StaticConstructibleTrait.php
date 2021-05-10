<?php

namespace PhpPlus\Core\Traits;

/**
 * A trait for classes that require static set-up before interaction.
 */
trait StaticConstructibleTrait
{
    /**
     * A boolean flag indicating whether static initialization has been completed for the class.
     * 
     * Random digits are appended to the end of the name to help ensure that no clashes occur
     * with any user-defined static properties.
     * 
     * This should not be overridden or touched in user code.
     * 
     * @var bool
     * @internal
     */
    private static bool $private_isInitializedIndicator_edbaZJGJySop2hBmiJvl = false;

    /**
     * Sets up the class before interaction.
     * 
     * This method should be called immediately after the class definition so that the class
     * can be set up. Calls to this method have no effect after the initial call.
     * 
     * To provide static initialization, implement the {@see self::__initStatic()} abstract method,
     * which will be called internally by this method.
     * 
     * This method should not be overridden.
     * 
     * @internal
     */
    public static function __constructStatic(): void
    {
        // Initialize the class if it has yet to be initialized
        if (!static::$private_isInitializedIndicator_edbaZJGJySop2hBmiJvl) {
            static::$private_isInitializedIndicator_edbaZJGJySop2hBmiJvl = true;
            static::__initStatic();
        } // Otherwise do nothing
    }

    /**
     * Sets up the class before interaction.
     * This method will be called internally by {@see self::__constructStatic()}.
     */
    protected static abstract function __initStatic(): void;
}
