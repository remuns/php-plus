<?php

namespace PhpPlus\Core\Control\Functions;

use Closure;
use PhpPlus\Core\Traits\StaticClassTrait;

/**
 * A static class containing helper functions for working with functions.
 */
final class Helpers
{
    use StaticClassTrait;

    /**
     * Converts the callable passed in to a Closure.
     * @param callable $f The callable to convert.
     * @return Closure
     */
    public static function callableToClosure(callable $f): Closure
    {
        if (is_object($f) && $f instanceof Closure) {
            // Avoid re-wrapping the Closure passed in
            return $f;
        } else {
            // Wrap the non-Closure callable in a Closure
            return fn (...$args) => call_user_func_array($f, $args);
        }
    }
}
