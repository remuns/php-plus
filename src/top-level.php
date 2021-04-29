<?php

use PhpPlus\Core\Control\Functions\Helpers;

if (!function_exists('callsure')) {
    /**
     * Converts the callable passed in to a Closure.
     * @param callable $f The callable to convert.
     * @return Closure
     */
    function callsure(callable $f) { return Helpers::callableToClosure($f); }
}
