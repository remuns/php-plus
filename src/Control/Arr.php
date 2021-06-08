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
     * @param bool      $allowAdditionalValues
     *                      Whether or not to allow (non-type-checked) keys in the array
     *                      passed in that will be ignored. Defaults to `false`.
     * @param bool      $throw  Whether or not to throw a {@see PhpPlusTypeError} on type-check
     *                          failure.  Defaults to `false`.
     * 
     * @return bool Whether or not the array passed the type-check.
     * 
     * @throws PhpPlusTypeError The array failed the type-check and `$throw` was `true`.
     */
    public static function typeCheckLooseStructure(
        array $array, array $types, bool $allowAdditionalValues = false, bool $throw = false): bool
    {
        // Ensure that the type array passed in contains types
        // This type check should always throw if it fails
        self::typeCheck($types, Types::meta(), throw: true);

        $arrayArrayKeys = array_keys($array);
        $typeArrayKeys = array_keys($types);
        
        if ($allowAdditionalValues) {
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
     * @param Option|null $additionalArgsType
     *                      Whether or not to allow additional keys in the array passed in.
     *                      An option wrapping a type will cause the additional arguments to be
     *                      type-checked as the value passed in.
     *                      An option wrapping `null` will cause the additional arguments to be
     *                      ignored by the type-check.
     *                      An empty option will disallow additional arguments.
     *                      Passing `null` in is permitted because a constant expression is required
     *                      to allow a default value; however, `null` values will be treated as
     *                      empty options and will disable allowing additional arguments as well.
     * @param bool      $throw  Whether or not to throw a {@see PhpPlusTypeError} on type-check
     *                          failure.  Defaults to `false`.
     * 
     * @return bool Whether or not the array passed the type-check.
     * 
     * @throws PhpPlusTypeError The array failed the type-check and `$throw` was `true`.
     */
    public static function typeCheckStrictStructure(
        array $array, array $types, ?Option $additionalArgsType = null, bool $throw = false): bool
    {
        // Ensure that the type array passed in contains types
        // This type check should always throw if it fails
        self::typeCheck($types, Types::meta(), throw: true);

        // Ensure that the option passed in wraps a type if it does not wrap null
        // This type check should always throw if it fails
        $additionalArgsType = Option::fromNullableOption($additionalArgsType);
        if (!$additionalArgsType->hasStrict(null)) {
            $additionalArgsType->typeCheck(Types::meta(), throw: true);
        }

        $arrayArrayKeys = array_keys($array);
        $typeArrayKeys = array_keys($types);
        $typeCount = count($types);
        
        if ($additionalArgsType->isSome && $typeCount < count($arrayArrayKeys)) {
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

        // Store whether or not additional type-checking is required
        $checkAdditionalKeys = $additionalArgsType->hasStrict(null) === false;
        $additionalType = $additionalArgsType->nValue;

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
                if (!$additionalType->has($value)) {
                    return $throw ?
                            throw new PhpPlusTypeError(
                                "array value did not match expected type {$additionalType}") :
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
