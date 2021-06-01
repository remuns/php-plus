<?php

namespace PhpPlus\Core\Types;

use InvalidArgumentException;
use PhpPlus\Core\Control\Arr;

/**
 * A type representing an array that is typed (i.e. all elements have the same type).
 * 
 * @property-read Type $type The type of elements contained in instances of this array type.
 */
final class TypedArrayType extends ArrayType
{
    use NonTrivialTypeTrait;

    /**
     * Constructs a new instance of the {@see self} class representing arrays that are typed
     * with the type passed in.
     */
    public function __construct(private Type $type) { }

    public function __get(string $name)
    {
        return match ($name) {
            'type' => $this->type,
            default => throw new InvalidArgumentException("property \"{$name}\" does not exist"),
        };
    }

    public function compare(Type $other): ?int
    {
        if ($other instanceof ArrayType) {
            if ($other instanceof self) {
                return $this->type->compare($other->type);
            } else {
                return -1; // Must be the base array type
            }
        } else {
            return $other->handleTrivialCompareCases();
        }
    }

    public function has($item): bool
    {
        return is_array($item) ? Arr::typeCheck($item, $this->type) : false;
    }

    public function __toString(): string
    {
        return "{$this->type}[]";
    }
}
