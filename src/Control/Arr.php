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
}
