<?php

use PhpPlus\Core\Control\Access;
use PhpPlus\Core\Control\Functions\Helpers as Funcs;

if (!function_exists('arr_nullable')) {
    /**
     * Accesses an array, returning null if the access fails.
     * 
     * @param array|ArrayAccess $array      The array to acccess.
     * @param string|int        ...$props   A param array of string and integer property names and
     *                                      indexes indicating the access to perform.
     * 
     * @return mixed    The array passed in if the property list is empty. Otherwise, the value
     *                  returned if the properties indicated are defined, and null if any property
     *                  is not defined.
     */
    function arr_nullable(array|ArrayAccess $array, string|int ...$props): mixed
    {
        return Access::arrayNullableArray($array, $props);
    }
}

if (!function_exists('arr_nullable_array')) {
    /**
     * Accesses an array, returning null if the access fails.
     * 
     * @param array|ArrayAccess $array  The array to acccess.
     * @param (string|int)[]    $props  An array of string and integer property names and
     *                                  indexes indicating the access to perform.
     * 
     * @return mixed    The array passed in if the property list is empty. Otherwise, the value
     *                  returned if the properties indicated are defined, and null if any property
     *                  is not defined.
     */
    function arr_nullable_array(array|ArrayAccess $array, array $props): mixed
    {
        return Access::arrayNullableArray($array, $props);
    }
}

if (!function_exists('callsure')) {
    /**
     * Converts the callable passed in to a Closure.
     * @param callable $f The callable to convert.
     * @return Closure
     */
    function callsure(callable $f) { return Funcs::callableToClosure($f); }
}
