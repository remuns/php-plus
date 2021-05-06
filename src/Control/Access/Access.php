<?php

namespace PhpPlus\Core\Control\Access;

use PhpPlus\Core\Traits\StaticClassTrait;

/**
 * A static class storing functions and methods for simply and easily accessing properties and
 * methods dynamically.
 */
final class Access
{
    use StaticClassTrait;

    /**
     * Accesses an object or array using a series of method, property or offset segments.
     * 
     * @param array|object                      $val    The item to access.
     * @param AccessSegment|array|string    ...$accessSegments
     *                                              A parameter array of values describing the
     *                                              access to make.
     *                                              If a property is desired, such a value should
     *                                              be the string name of the property.
     *                                              If a method call is desired, this should be an
     *                                              array containing the string name of the method
     *                                              as the first element, with the arguments
     *                                              included as subsequent array elements.
     *                                              If an array or ArrayAccess offset is desired,
     *                                              this should be an array containing only the
     *                                              offset to be accessed.
     *                                              An already-made access segment can also
     *                                              be included.
     * @return mixed The result of the access.
     */
    public static function access(
        array|object $val, AccessSegment|array|string ...$accessSegments)
    {
        // Parse anything that isn't already an access segment before proceeding
        // We want to parse first so that no exception or error can be thrown due to an
        // undefined property or method (or thrown due to method logic) and mask malformed input.
        $accessSegments = array_map(AccessSegment::class.'::create', $accessSegments);

        $newVal = $val;
        foreach ($accessSegments as $segment) {
            $newVal = $segment->apply($newVal);
        }
        return $newVal;
    }

    /**
     * Accesses an object or array using a series of method, property or offset segments,
     * returning null if any null values are encountered rather than continuing the access.
     * 
     * @param array|object                      $val    The item to access.
     * @param AccessSegment|array|string    ...$accessSegments
     *                                              A parameter array of values describing the
     *                                              access to make.
     *                                              If a property is desired, such a value should
     *                                              be the string name of the property.
     *                                              If a method call is desired, this should be an
     *                                              array containing the string name of the method
     *                                              as the first element, with the arguments
     *                                              included as subsequent array elements.
     *                                              If an array or ArrayAccess offset is desired,
     *                                              this should be an array containing only the
     *                                              offset to be accessed.
     *                                              An already-made access segment can also
     *                                              be included.
     * @return mixed The result of the access, or null if it was encountered during the access.
     */
    public static function accessNullable(
        array|object $val, AccessSegment|array|string ...$accessSegments)
    {
        // Parse anything that isn't already an access segment before proceeding
        // We want to parse first so that no exception or error can be thrown due to an
        // undefined property or method (or thrown due to method logic) and mask malformed input.
        $accessSegments = array_map(AccessSegment::class.'::create', $accessSegments);

        $newVal = $val;
        foreach ($accessSegments as $segment) {
            if ($newVal === null) {
                break;
            } else {
                $newVal = $segment->apply($newVal);
            }
        }
        return $newVal;
    }

    /**
     * Accesses an object or array using a series of method, property or offset segments,
     * returning null if any non-accessible value if encountered rather than continuing the access.
     * 
     * For example, if an attempt to get an object property from any non-object is made, the
     * access will stop and null will be returned.
     * 
     * 
     * @param array|object                      $val    The item to access.
     * @param AccessSegment|array|string    ...$accessSegments
     *                                              A parameter array of values describing the
     *                                              access to make.
     *                                              If a property is desired, such a value should
     *                                              be the string name of the property.
     *                                              If a method call is desired, this should be an
     *                                              array containing the string name of the method
     *                                              as the first element, with the arguments
     *                                              included as subsequent array elements.
     *                                              If an array or ArrayAccess offset is desired,
     *                                              this should be an array containing only the
     *                                              offset to be accessed.
     *                                              An already-made access segment can also
     *                                              be included.
     * @return mixed    The result of the access, or null if any non-accessible value was
     *                  encountered during the access.
     */
    public static function accessAccessible(
        array|object $val, AccessSegment|array|string ...$accessSegments)
    {
        // Parse anything that isn't already an access segment before proceeding
        // We want to parse first so that no exception or error can be thrown due to an
        // undefined property or method (or thrown due to method logic) and mask malformed input.
        $accessSegments = array_map(AccessSegment::class.'::create', $accessSegments);

        $newVal = $val;
        foreach ($accessSegments as $segment) {
            if ($segment->canApply($newVal)) {
                $newVal = $segment->apply($newVal);
            } else {
                return null;
            }
        }
        return $newVal;
    }

    /**
     * Accesses an object or array using a series of method, property or offset segments,
     * returning null if any value for which an access is not defined is encountered rather than
     * continuing the access.
     * 
     * For example, if an attempt is made to access an undefined object property on an object,
     * the access will stop and null will be returned.  Also, if any attempt is made to
     * access a non-object or array, null will similarly be returned.
     * 
     * @param array|object                      $val    The item to access.
     * @param AccessSegment|array|string    ...$accessSegments
     *                                              A parameter array of values describing the
     *                                              access to make.
     *                                              If a property is desired, such a value should
     *                                              be the string name of the property.
     *                                              If a method call is desired, this should be an
     *                                              array containing the string name of the method
     *                                              as the first element, with the arguments
     *                                              included as subsequent array elements.
     *                                              If an array or ArrayAccess offset is desired,
     *                                              this should be an array containing only the
     *                                              offset to be accessed.
     *                                              An already-made access segment can also
     *                                              be included.
     * @return mixed    The result of the access, or null if any value for which access is not
     *                  defined is encountered during the access.
     */
    public static function accessDefined(
        array|object $val, AccessSegment|array|string ...$accessSegments)
    {
        // Parse anything that isn't already an access segment before proceeding
        // We want to parse first so that no exception or error can be thrown due to an
        // undefined property or method (or thrown due to method logic) and mask malformed input.
        $accessSegments = array_map(AccessSegment::class.'::create', $accessSegments);

        $newVal = $val;
        foreach ($accessSegments as $segment) {
            if ($segment->isApplyDefined($newVal)) {
                $newVal = $segment->apply($newVal);
            } else {
                return null;
            }
        }
        return $newVal;
    }
}
