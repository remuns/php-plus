<?php

namespace PhpPlus\Core\Control;

use Illuminate\Support\Arr as BaseArr;

use PhpPlus\Core\Traits\StaticClassTrait;
use PhpPlus\Core\Traits\WellDefinedStatic;

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
     * Type-checks the array passed in to ensure all arguments are of the specified type.
     * 
     * @param array $array  The array to type-check.
     * @param Type  $type   The type to check the array elements for.
     * @param bool  $throw  Whether or not to throw a {@see PhpPlusTypeError} on
     *                      type-check failure.
     * 
     * @return bool Whether or not the array passed the type-check.
     * 
     * @throws PhpPlusTypeError The array failed the type-check and $throw was `true`.
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
}
