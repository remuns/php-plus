<?php

namespace PhpPlus\Core\Traits;

/**
 * A trait for classes that store static functionality only and should not be instantiated.
 * 
 * This trait provides a private final parameterless constructor that should not be called at all.
 * The constructor is provided to ensure that contexts outside the definition of the class using
 * the trait cannot construct the type, but it can still be called internally.
 */
trait StaticClassTrait
{
    /**
     * This class should never be constructed.
     * @internal This function should never be called.
     * @deprecated This function should never be called.
     */
    public final function __construct() { }
}
