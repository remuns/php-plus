<?php

namespace PhpPlus\Core\Traits;

/**
 * A trait for classes with a private final parameterless constructor.
 * 
 * These classes should never be instantiated outside of the class.  The constructor should
 * not be redefined outside of this trait, and no other constructors should be supplied.
 */
trait InternalDefaultConstructibleTrait
{
    /**
     * This class should never be constructed externally.
     * @internal This function should never be called outside of this class.
     */
    private final function __construct() { }
}
