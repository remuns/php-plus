<?php

namespace PhpPlus\Core\Types;

use InvalidArgumentException;
use LogicException;
use PhpPlus\Core\Control\Arr;
use PhpPlus\Core\Control\Comparison;
use PhpPlus\Core\Control\Option;
use TypeError;

/**
 * Represents an array type that conforms to a loose type structure.
 * @see Arr::typeCheckLooseStructure
 * 
 * @property-read Type      $extraKeysType
 *                                  The type of additional arguments to be allowed in the array.
 * @property-read Type[]    $types  An array describing the type structure of the array.
 */
final class LooseStructuredArrayType extends ArrayType
{
    use NonTrivialTypeTrait;

    /**
     * Constructs a new instance of the {@see self} class.
     * 
     * @param Type[]    $types          A key-value mapping of keys to types describing the
     *                                  structure of the array.
     * @param Type      $extraKeysType
     *                                  A type describing the type of additional keys allowed in
     *                                  the array.
     *                                  Note that passing in the `nothing` type is equivalent to
     *                                  preventing additional keys.
     */
    public function __construct(private array $types, private Type $extraKeysType)
    {
        Arr::typeCheck($types, Types::meta(), throw: true);

        // Don't allow Types::nothing() as a type that keys are mapped to, as this would make
        // this an empty type
        // Such types should be considered malformed and should not be allowed
        $nothingType = Types::nothing();
        foreach ($types as $type) {
            if ($type == $nothingType) {
                throw new TypeError(
                    'cannot create a structured array type with a key mapped to the ' .
                        '"nothing" type (as such a type would have no instances)');
            }
        }
    }

    public function __get(string $name)
    {
        return match ($name) {
            'extraKeysType' => $this->extraKeysType,
            'types' => $this->types,
            default => throw new InvalidArgumentException("undefined property \"{$name}\""),
        };
    }

    public function has($item): bool
    {
        return is_array($item) ?
                Arr::typeCheckLooseStructure($item, $this->types, $this->extraKeysType) :
                false;
    }

    public function compare(Type $other): ?int
    {
        if ($other instanceof ArrayType) {
            if ($other == BaseArrayType::value()) {
                return -1;
            } elseif ($other instanceof self) {
                $extraTypeComparison = $this->extraKeysType->compare($other->extraKeysType);
                if ($extraTypeComparison === null) {
                    return null;
                }

                /*
                 * An array is a subtype of another if the following conditions are met:
                 * 1. All keys in both arrays are attached to types in the first array that are
                 *    subtypes of the corresponding types in the second array
                 *    to the same keys in the second
                 * 2. All keys in the first array that are not present in the second array are
                 *    subtypes of the additional argument type of the second array
                 * 3. There are no keys present in only the second array
                 * 4. The additional args type of the first array is a subtype of the additional
                 *    args type of the second array
                 */
                $diff = Arr::keyDiff($this->types, $other->types);

                if ($diff[0] !== [] && $diff[1] !== []) {
                    // There are keys in each array that are not shared by the other, so the two
                    // types do not compare, since an array of the first type can lack a key from
                    // the second (and thus is not an instance of the second type) and vice-versa
                    return null;
                }

                if ($diff[0] !== [] && $extraTypeComparison > 0) {
                    // There are keys in the first array that aren't in the second (indicating a
                    // subtype relationship), but the extra keys type of the first array is a
                    // supertype of the extra keys type of the second (indicating a supertype
                    // relationship), so the 2 do not compare
                    return null;
                }

                if ($diff[1] !== [] && $extraTypeComparison < 0) {
                    // There are keys in the second array that aren't in the first (indicating a
                    // subtype relationship), but the extra keys type of the second array is a
                    // supertype of the extra keys type of the first (indicating a supertype
                    // relationship), so the 2 do not compare
                    return null;
                }

                $finalComparison = $extraTypeComparison;
                // Check the keys shared by both instances
                foreach ($diff[2] as $sharedK) {
                    $comparison = $this->types[$sharedK]->compare($other->types[$sharedK]);
                    if ($comparison === null) {
                        return null; // Two keys have incompatible types
                    } elseif ($finalComparison === 0) {
                        if ($comparison !== 0) {
                            // Found a non-trivial (non-equal) comparison that constrains the type
                            // of the array
                            $finalComparison = $comparison;
                        }
                    } elseif (
                        $comparison !== 0 &&
                            Comparison::fromSpaceship($comparison) !=
                                Comparison::fromSpaceship($finalComparison))
                    {
                        // Found a difference between the constraints on the two types that makes
                        // them incomparable
                        return null;
                    }
                }

                if ($finalComparison > 0 && $diff[0] !== []) {
                    // There are array keys that exist only in the first array, but shared keys
                    // that are strict supertypes in the first array
                    // The array types don't compare since an instance of the first array type
                    // that contains a key mapping to the strict supertype is not an instance
                    // of the second array type, but an instance of the second array type that
                    // lacks keys required by the first array type is likewise not an instance of
                    // the first array type
                    return null;
                }

                if ($finalComparison < 0 && $diff[1] !== []) {
                    // There are array keys that exist only in the second array, but shared keys
                    // that are strict supertypes in the second array
                    // The array types don't compare since an instance of the second array type
                    // that contains a key mapping to the strict supertype is not an instance
                    // of the first array type, but an instance of the first array type that
                    // lacks keys required by the second array type is likewise not an instance of
                    // the second array type
                    return null;
                }

                /*
                 * Run through the additional keys that exist in one of the arrays
                 * Since the arrays don't compare if there are keys in each array that are not
                 * shared by the other (and that case has already been filtered out), only 1 of
                 * the following loops should run
                 */

                // Run through the additional keys in the first array if any exist
                // In this case 
                foreach ($diff[0] as $kT) {
                    $comparison = $this->types[$kT]->compare($other->extraKeysType);
                    if ($comparison === null) {
                        return null;
                    } elseif ($comparison > 0) {
                        // There is a key only in the first array type that is a strict supertype
                        // of the extra keys type of the second array type, so an instance of the
                        // second array type that lacks the key is not an element of the first
                        // array type and an instance of the first array type that maps the key
                        // to the strict supertype is not an instance of the second array type
                        // Therefore, the types do not compare
                        return null;
                    }
                    
                    // The fact that this loop runs means that there are keys in the first
                    // array type that are not in the second
                    // Therefore, either the first array is a subtype of the second or they
                    // do not compare
                    $finalComparison = -1;
                }

                // Run through the additional keys in the second array if any exist
                foreach ($diff[1] as $kO) {
                    $comparison = $this->extraKeysType->compare($other->types[$kO]);
                    if ($comparison === null) {
                        return null;
                    } elseif ($comparison < 0) {
                        // There is a key only in the second array type that is a strict supertype
                        // of the extra keys type of the first array type, so an instance of the
                        // first array type that lacks the key is not an element of the second
                        // array type and an instance of the second array type that maps the key
                        // to the strict supertype is not an instance of the first array type
                        // Therefore, the types do not compare
                        return null;
                    }

                    // The fact that this loop runs means that there are keys in the second
                    // array type that are not in the first
                    // Therefore, either the second array is a subtype of the first or they
                    // do not compare
                    $finalComparison = 1;
                }

                return $finalComparison;
            } else {
                // Right now there should be no other comparisons to handle
                throw new LogicException(
                    'should be no other array type comparison cases to handle');
            }
        } else {
            return $other->handleTrivialCompareCases();
        }
    }

    public function __toString(): string
    {
        if (empty($this->types)) {
            return '[]';
        } else {
            $str =
                "[{$this->formatArrayKey(array_keys($this->types)[0])}: " .
                    $this->types[0];
            $leftoverTypes = array_slice($this->types, 1, preserve_keys: true);
            foreach ($leftoverTypes as $key => $type) {
                $str .= ", {$this->formatArrayKey($key)}: {$type}";
            }

            if ($this->extraKeysType != Types::nothing()) {
                if ($this->extraKeysType == Types::anything()) {
                    $str .= ' ...';
                } else {
                    $str .= " ...{$this->extraKeysType}";
                }
            }
            $str .= ']';
        }

        return $str;
    }

    private function formatArrayKey(int|string $key): string
    {
        if (is_string($key)) {
            return "\"{$key}\"";
        } else {
            return $key;
        }
    }
}
