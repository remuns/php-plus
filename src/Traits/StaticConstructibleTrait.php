<?php

namespace PhpPlus\Core\Traits;

/**
 * A trait for classes that require static set-up before interaction.
 */
trait StaticConstructibleTrait
{
    private static bool $private_isInitializedIndicator = false;

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
        if (!self::$private_isInitializedIndicator) {
            self::$private_isInitializedIndicator = true;
            self::__initStatic();
        } // Otherwise do nothing
    }

    /**
     * Sets up the class before interaction.
     * This method will be called internally by {@see self::__constructStatic()}.
     */
    protected static abstract function __initStatic(): void;
}
