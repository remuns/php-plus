<?php

namespace PhpPlus\Core\Control;

use Illuminate\Support\Arr as BaseArr;

use PhpPlus\Core\Traits\StaticClassTrait;
use PhpPlus\Core\Traits\WellDefinedStatic;
use PhpPlus\Core\Types\PhpPlusTypeError;
use PhpPlus\Core\Types\Type;
use PhpPlus\Core\Types\Types;

/**
 * A static class offering array helper methods.
 * 
 * This class extends the Illuminate framework Arr helper class.
 * 
 * @see Illuminate\Support\Arr
 */
class Arr extends BaseArr
{
    use StaticClassTrait;
    use WellDefinedStatic;

    /**
     * An array representing the result of two-sided array diff functions when the two arrays
     * are equal with respect to the function.
     * 
     * @see self::keyDiff
     * 
     * @var array
     */
    const SAME_ARR_RESULT = [[], []];

    /**
     * Zips the arrays passed in together into a single array.
     * 
     * Unlike the array_map function when a null argument is passed, this function will omit any
     * array values that are not represented by keys.
     * 
     * @param array ...$arrays The list of arrays to merge.
     * 
     * @return array A sequential array containing the values of the arrays zipped together.
     */
    public static function zip(array ...$arrays)
    {
        // Remove the trivial case of an empty array
        // If we're continuing, the array is non-empty
        if (empty($arrays)) {
            return [];
        }

        // Strip the keys out of the arrays
        $arrays = array_map('array_values', $arrays);

        // Retrieve the minimum length of all arrays passed in
        $numArrays = count($arrays);
        $minCount = $arrays[0];
        for ($i = 0; $i < $numArrays; $i++) {
            $minCount = min($minCount, count($arrays[$i]));
        }

        // Construct the result
        $result = [];
        for ($i = 0; $i < $minCount; $i++) {
            $value = [];
            for ($j = 0; $j < $numArrays; $j++) {
                $value[] = $arrays[$j][$i];
            }
            $result[] = $value;
        }
        return $result;
    }

    /**
     * Type-checks the array passed in based on an array of type arguments that describe the
     * structure of the array.
     * 
     * This method type-checks the array as a loose comparison, i.e. the order of the keys
     * checked does not matter.
     * 
     * @param array     $array  The array to type-check.
     * @param Type[]    $types  An array of keys mapped to types to check for.
     *                          The keys should correspond to keys in the $array argument.
     * @param Type|null $extraKeysType
     *                      A type describing the type of values assigned to extra keys allowed
     *                      in the array, or `null` if no extra keys are permitted.
     *                      This defaults to `null`, which is the equivalent of passing in
     *                      {@see Types::nothing()}.
     * @param bool      $throw  Whether or not to throw a {@see PhpPlusTypeError} on type-check
     *                          failure.  Defaults to `false`.
     * 
     * @return bool Whether or not the array passed the type-check.
     * 
     * @throws PhpPlusTypeError The array failed the type-check and `$throw` was `true`.
     */
    public static function typeCheckLooseStructure(
        array $array, array $types, ?Type $extraKeysType = null, bool $throw = false): bool
    {
        // Ensure that the type array passed in contains types
        // This type check should always throw if it fails
        self::typeCheck($types, Types::meta(), throw: true);

        $arrayArrayKeys = array_keys($array);
        $typeArrayKeys = array_keys($types);

        // Replace null with the functional equivalent (since Types::nothing() will allow no
        // additional values)
        if ($extraKeysType === null) {
            $extraKeysType = Types::nothing();
        }

        // Allow additional keys if the extra keys type is not nothing (since in that case
        // it will reject all other arguments)
        $allowAdditionalKeys = $extraKeysType != Types::nothing();

        if ($allowAdditionalKeys) {
            // Ensure that the type array keys are a subset of the array keys, or else the
            // type-check cannot possibly succeed
            if (!empty(array_diff($typeArrayKeys, $arrayArrayKeys))) {
                return $throw ?
                        throw new PhpPlusTypeError(
                            'the type array argument had some keys that were not present in ' .
                                'the array argument') :
                        false;
            }
        } else {
            // Ensure the keys of the array and type array match exactly
            if (self::sortReturn($arrayArrayKeys) != self::sortReturn($typeArrayKeys))
            {
                return $throw ?
                        throw new PhpPlusTypeError(
                            'the array argument and the type array argument had some keys ' .
                                'that did not match') :
                        false;
            }
        }

        // Need to check additional keys if the extra key type will not allow any value
        $checkAdditionalKeys = $extraKeysType != Types::anything();

        // Check all the values of the array by key
        foreach ($array as $key => $value) {
            // Check the type if the type array offset exists
            // Don't bother doing anything otherwise; if additional values are not allowed then
            // the method would have returned / errored out by now
            if (isset($types[$key])) {
                $type = $types[$key];
                if (!$type->has($value)) {
                    return $throw ?
                        throw new PhpPlusTypeError(
                            "array value did not match expected type {$type}") :
                        false;
                }
            } else if ($checkAdditionalKeys) {
                if (!$extraKeysType->has($value)) {
                    return $throw ?
                            throw new PhpPlusTypeError(
                                "array value did not match expected type {$extraKeysType}") :
                            false;
                }
            }
        }
        return true;
    }

    /**
     * Type-checks the array passed in based on an array of type arguments that describe the
     * structure of the array.
     * 
     * This method type-checks the array as a strict comparison, i.e. the order of the keys
     * does matter.
     * 
     * @param array     $array  The array to type-check.
     * @param Type[]    $types  An array of keys mapped to types to check for.
     *                          The keys should correspond to keys in the $array argument.
     * @param Type|null $extraKeysType
     *                      A type describing the type of values assigned to extra keys allowed
     *                      in the array, or `null` if no extra keys are permitted.
     *                      This defaults to `null`, which is the equivalent of passing in
     *                      {@see Types::nothing()}.
     * @param bool      $throw  Whether or not to throw a {@see PhpPlusTypeError} on type-check
     *                          failure.  Defaults to `false`.
     * 
     * @return bool Whether or not the array passed the type-check.
     * 
     * @throws PhpPlusTypeError The array failed the type-check and `$throw` was `true`.
     */
    public static function typeCheckStrictStructure(
        array $array, array $types, ?Type $extraKeysType = null, bool $throw = false): bool
    {
        // Ensure that the type array passed in contains types
        // This type check should always throw if it fails
        self::typeCheck($types, Types::meta(), throw: true);

        // Replace null extra key type with the `nothing` type
        if ($extraKeysType === null) {
            $extraKeysType = Types::nothing();
        }

        $arrayArrayKeys = array_keys($array);
        $typeArrayKeys = array_keys($types);
        $typeCount = count($types);

        // Additional keys are allowed if the type they are checked as contains a value
        // The only type that satisfies this condition is the `nothing` type
        $additionalKeysAllowed = $extraKeysType != Types::nothing();
        
        if ($additionalKeysAllowed && $typeCount < count($arrayArrayKeys)) {
            // Ensure that the type array keys are exactly equal to a slice of the array, or else
            // the type-check cannot possibly succeed
            if (array_slice($arrayArrayKeys, 0, $typeCount) !== $typeArrayKeys) {
                return $throw ?
                        throw new PhpPlusTypeError(
                            'the type array argument had some keys that were not present in ' .
                                'the same positions in the array argument') :
                        false;
            }
        } else {
            // Ensure the keys of the array and type array match exactly
            if ($arrayArrayKeys !== $typeArrayKeys) {
                return $throw ?
                        throw new PhpPlusTypeError(
                            'the array argument and the type array argument had some keys ' .
                                'that did not match') :
                        false;
            }
        }

        // Need to type-check additional keys if the type for extra keys doesn't describe
        // all values
        $checkAdditionalKeys = $extraKeysType != Types::anything();

        // Check all the values of the array by key
        foreach ($array as $key => $value) {
            // Check the type if the type array offset exists
            if (isset($types[$key])) {
                $type = $types[$key];
                if (!$type->has($value)) {
                    return $throw ?
                            throw new PhpPlusTypeError(
                                "array value did not match expected type {$type}") :
                            false;
                }
            // If the type array offset doesn't exist, then check the value against the extra
            // keys type (if extra keys are allowed)
            } else if ($checkAdditionalKeys) {
                if (!$extraKeysType->has($value)) {
                    return $throw ?
                            throw new PhpPlusTypeError(
                                "array value did not match expected type {$extraKeysType}") :
                            false;
                }
            }
        }
        return true; // Have checked everything
    }

    /**
     * Type-checks the array passed in to ensure all arguments are of the specified type.
     * 
     * @param array $array  The array to type-check.
     * @param Type  $type   The type to check the array elements for.
     * @param bool  $throw  Whether or not to throw a {@see PhpPlusTypeError} on
     *                      type-check failure.
     * 
     * @return bool Whether or not the array passed the type-check.
     * 
     * @throws PhpPlusTypeError The array failed the type-check and `$throw` was `true`.
     */
    public static function typeCheck(array $array, Type $type, bool $throw = false)
    {
        foreach ($array as $val) {
            if (!$type->has($val)) {
                if ($throw) {
                    throw new PhpPlusTypeError(
                        "expected array element to be an instance of type {$type}");
                } else {
                    return false;
                }
            }
        }
        return true;
    }

    /**
     * Gets the difference between the two arrays.
     * 
     * @param array $arr0 The first array to compare.
     * @param array $arr1 The second array to compare.
     * 
     * @return array    An array containing an array of keys present in `$arr0` but not `$arr1`
     *                  at index 0 and an array of keys present in `$arr1` but not `$arr0` at
     *                  index 1.
     * 
     *                  If the arrays contain exactly the same keys, the result of the function
     *                  will be `[[], []]`.
     */
    public static function keyDiff(array $arr0, array $arr1): array
    {
        $keys0 = array_keys($arr0);
        $keys1 = array_keys($arr1);

        $diff0 = [];
        $diff1 = [];

        // Get keys in $arr0 that are not in $arr1
        foreach ($keys0 as $k0) {
            if (!array_key_exists($k0, $arr1)) {
                $diff0[] = $k0;
            }
        }

        // Get the keys in $arr1 that are not in $arr0
        foreach ($keys1 as $k1) {
            if (!array_key_exists($k1, $arr0)) {
                $diff1[] = $k1;
            }
        }

        return [$diff0, $diff1];
    }

    /**
     * Returns the result of sorting the array using the {@see sort()} function, but returning
     * the result rather than passing the array by reference.
     * 
     * @param array $arr The array to sort.
     * @return array
     */
    public static function sortReturn(array $arr): array
    {
        $ret = $arr;
        sort($ret);
        return $ret;
    }
}
