<?php

namespace PhpPlus\Core\Control;

use PhpPlus\Core\Traits\StaticClassTrait;

use TypeError;

/**
 * A static class storing functions and methods for simply and easily accessing properties and
 * methods dynamically.
 */
final class Access
{
    use StaticClassTrait;

    /**
     * Accesses an object by either accessing properties with the names passed in or
     * calling named methods passed in.
     * 
     * @param object        $val                The object to access.
     * @param string|array  ...$accessSegments  A parameter array of values describing the access
     *                                          to make.
     *                                          If a property is desired, such a value should be
     *                                          the string name of the property.
     *                                          If a method call is desired, this should be an
     *                                          array containing the string name of the method as
     *                                          the first element, with the arguments included as
     *                                          subsequent array elements.
     * @return mixed The result of the access.
     */
    public static function access(object $val, string|array ...$accessSegments)
    {
        // Type check the segments quickly before running
        // We want to type-check first so that no exception or error can be thrown due to an
        // undefined property or method (or thrown due to method logic) and mask malformed input.
        array_walk($accessSegments, 'self::typeCheckSegment');

        $newVal = $val;
        foreach ($accessSegments as $segment) {
            // Disable the type-checking for the arrays since they have already been type-checked
            $newVal = self::accessOnceInternal($newVal, $segment, typeCheck: false);
        }

        return $newVal;
    }

    /**
     * Accesses an object by either accessing properties with the names passed in or
     * calling named methods passed in.
     * 
     * @param object    $val            The object to access.
     * @param array     $accessSegments An array of values describing the access to make.
     *                                  If a property is desired, the array should include the
     *                                  string name of the property.
     *                                  If a method call is desired, the array should include an
     *                                  array containing the string name of the method as the
     *                                  first element, with the arguments included as subsequent
     *                                  array elements.
     * @return mixed The result of the access.
     */
    public static function accessArray(object $val, array $accessSegments)
    {
        // Type check the segments quickly before running
        // We want to type-check first so that no exception or error can be thrown due to an
        // undefined property or method (or thrown due to method logic) and mask malformed input.
        array_walk($accessSegments, 'self::typeCheckSegment');

        $newVal = $val;
        foreach ($accessSegments as $segment) {
            // Disable the type-checking for the arrays since they have already been type-checked
            $newVal = self::accessOnceInternal($newVal, $segment, typeCheck: false);
        }

        return $newVal;
    }

    /**
     * Accesses an object by either accessing the property with the name passed in or
     * calling the named method passed in.
     * 
     * @param object        $val            The object to access.
     * @param string|array  $accessSegment  A value describing the access to make.
     *                                      If a property is desired, this should be the string
     *                                      name of the property.
     *                                      If a method call is desired, this should be an array
     *                                      containing the string name of the method as the first
     *                                      element, with the arguments included as subsequent
     *                                      array elements.
     * @return mixed The result of the access.
     */
    public static function accessOnce(object $val, string|array $accessSegment)
    {
        return self::accessOnceInternal($val, $accessSegment, typeCheck: true);
    }

    private static function accessOnceInternal(
        object $val, string|array $accessSegment, bool $typeCheck)
    {
        if (is_string($accessSegment)) {
            return $val->$accessSegment;
        } else {
            if ($typeCheck) { self::typeCheckFuncSegment($accessSegment); }

            if (count($accessSegment) === 1) {
                return $val->{$accessSegment[0]}();
            } else {
                return call_user_func_array(
                        [$val, $accessSegment[0]], array_slice($accessSegment, 1));
            }
        } 
    }

    /**
     * Type-checks the access segment passed in.
     * @param string|array $accessSegment   Either a string property name or an array containing
     *                                      a string method name and additional arguments.
     */
    public static function typeCheckSegment(string|array $accessSegment): void
    {
        if (is_array($accessSegment)) {
            if (count($accessSegment) === 0 || !is_string($accessSegment[0])) {
                throw new TypeError(
                    'expected an array with a string method name as its first value');
            }
        }
    }

    private static function typeCheckFuncSegment(array $accessSegment): void
    {
        if (count($accessSegment) === 0 || !is_string($accessSegment[0])) {
            throw new TypeError(
                'expected an array with a string method name as its first value');
        }
    }
}
