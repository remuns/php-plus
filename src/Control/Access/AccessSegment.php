<?php

namespace PhpPlus\Core\Control\Access;

use ArrayAccess;
use InvalidArgumentException;
use LogicException;
use PhpPlus\Core\Traits\WellDefinedSelf;

/**
 * A class representing segments of access paths that can be used to access a tree of objects
 * and arrays.
 */
final class AccessSegment
{
    use WellDefinedSelf;

    public const ARR_OFFSET_SEGMENT = 1;
    public const OBJ_PROPERTY_SEGMENT = 2;
    public const METHOD_CALL_SEGMENT = 4;

    private function __construct(private string|int|array $descriptor, private int $type) { }

    /**
     * Gets whether or not this segment indicates a call to a defined method.
     * @return bool
     */
    public function isCall(): bool { return $this->type === self::METHOD_CALL_SEGMENT; }

    /**
     * Gets whether or not this segment indicates access of an array or ArrayAccess.
     * @return bool
     */
    public function isOffset(): bool { return $this->type === self::ARR_OFFSET_SEGMENT; }

    /**
     * Gets whether or not this segment indicates access of an object property.
     * @return bool
     */
    public function isProp(): bool { return $this->type === self::OBJ_PROPERTY_SEGMENT; }

    /**
     * Gets an integer indicator of the type of this access segment.
     * @return int  One of {@see self::ARR_OFFSET_SEGMENT}, {@see self::METHOD_CALL_SEGMENT}
     *              or {@see self::OBJ_PROPERTY_SEGMENT} based on the type of this segment.
     */
    public function type(): int { return $this->type; }

    /**
     * Checks if applying this access segment represents a defined operation.
     * 
     * Note that method calls or calls to {@see ArrayAccess::offsetGet()} could still fail due
     * to an exception.  This method simply determines if the access is defined.
     * 
     * @param mixed $item The item to test.
     * 
     * @return bool
     */
    public function isApplyDefined(mixed $item): bool
    {
        // Check the type of item before checking if the specific access is defined
        if ($this->canApply($item)) {
            return match ($this->type) {
                self::ARR_OFFSET_SEGMENT =>
                    is_array($item) ?
                        isset($item[$this->descriptor]) :
                        $item->offsetExists($this->descriptor),
                self::METHOD_CALL_SEGMENT =>
                    method_exists($item, $this->descriptor[0]),
                self::OBJ_PROPERTY_SEGMENT =>
                    property_exists($item, $this->descriptor),
                default => throw self::invalidSegment(),
            };
        } else {
            return false;
        }
    }

    /**
     * Determines if this access segment can be applied to the item passed in based on its type.
     * @param mixed $item The item to test.
     * @return bool Whether or not this access segment can be applied to the item passed in.
     */
    public function canApply(mixed $item): bool
    {
        return match ($this->type) {
            self::ARR_OFFSET_SEGMENT => is_array($item) || $item instanceof ArrayAccess,
            self::METHOD_CALL_SEGMENT, self::OBJ_PROPERTY_SEGMENT => is_object($item),
            default => throw self::invalidSegment(),
        };
    }
    
    /**
     * Applies this access segment to the item passed in.
     * @param mixed $item The item to access.
     * @return mixed The result of the access.
     */
    public function apply($item): mixed
    {
        return match ($this->type) {
            self::ARR_OFFSET_SEGMENT => $item[$this->descriptor],
            self::OBJ_PROPERTY_SEGMENT => $item->{$this->descriptor},
            self::METHOD_CALL_SEGMENT =>
                $item->{$this->descriptor[0]}(...array_slice($this->descriptor, 1)),
            default => throw self::invalidSegment(),
        };
    }

    public function __toString(): string
    {
        return match ($this->type) {
            self::ARR_OFFSET_SEGMENT => '['.self::formatArrayDescriptor($this->descriptor).']',
            self::OBJ_PROPERTY_SEGMENT => "->{{$this->descriptor}}",
            self::METHOD_CALL_SEGMENT =>
                "->{{$this->descriptor[0]}}(" .
                    self::formatMethodArgs(array_slice($this->descriptor, 1)) .
                    ')',
            default => throw self::invalidSegment(),
        };
    }
    private static function formatArrayDescriptor(string|int $arg)
    {
        return is_string($arg) ? "\"$arg\"" : $arg;
    }
    private static function formatMethodArgs(array $args)
    {
        return implode(',', $args);
    }

    /**
     * Creates an access segment from the argument passed in.
     * 
     * This method is useful for parsing access segment parameter lists that could include
     * simple integers or strings intended to be access segments.
     * 
     * @param array|int|string|self $arg    An argument describing the access segment to create.
     *                                      The argument should be either:
     *                                      * An already-made access segment
     *                                      * An array containing a string method name and method
     *                                       call parameters
     *                                      * An integer representing an integer array offset
     *                                      * A string representing either:
     *                                          * A string property name (indicated by a leading
     *                                            '->', i.e. '->property')
     *                                          * A string array offset (indicated by wrapping
     *                                            '[]', i.e. '[offset]')
     * 
     * @return self The access segment passed in if one was passed in, otherwise the access
     *              segment parsed from the argument using {@see self::parse}.
     */
    public static function create(array|int|string|self $arg)
    {
        if ($arg instanceof self) {
            return $arg;
        } else {
            return self::parse($arg);
        }
    }

    /**
     * Parses an access segment from the argument passed in.
     * 
     * @param array|int|string $arg An argument describing the access segment to create.
     *                              The argument should be either:
     *                              * An array containing a string method name and method
     *                                call parameters
     *                              * An integer representing an integer array offset
     *                              * A string representing either:
     *                                  * A string property name (indicated by a leading '->',
     *                                    i.e. '->property')
     *                                  * A string array offset (indicated by wrapping '[]',
     *                                    i.e. '[offset]')
     * 
     * @return self
     */
    public static function parse(array|int|string $arg): self
    {
        if (is_string($arg)) {
            // Could be either a property or a string offset
            if (strlen($arg) < 3) {
                // Need at least '->' plus nonempty string, or '[]' wrapping a nonempty string
                throw self::badPropOrOffsetStr();
            }
            switch ($arg[0]) {
                case '-':
                    if ($arg[1] === '>') {
                        return self::prop(substr($arg, 2));
                    }
                    throw self::badPropOrOffsetStr();
                case '[':
                    if ($arg[-1] === ']') {
                        return self::offset(substr($arg, 1, -1));
                    }
                    throw self::badPropOrOffsetStr();
                default:
                    throw self::badPropOrOffsetStr();
            }
        } elseif (is_int($arg)) {
            return self::offset($arg);
        } else { // Must be an array
            if (empty($arg) || !is_string($arg[0])) {
                throw self::badMethodArray();
            }
            return self::call($arg[0], ...array_slice($arg, 1));
        }
    }

    private static function badPropOrOffsetStr(): InvalidArgumentException
    {
        return new InvalidArgumentException('invalid property or offset access segment');
    }

    private static function badMethodArray(): InvalidArgumentException
    {
        return new InvalidArgumentException('invalid method access segment');
    }

    private static function invalidSegment(): LogicException
    {
        return new LogicException('invalid access segment encountered');
    }

    /**
     * Creates a new array offset access segment with the offset passed in.
     * @param string|int $offset The offset into the array to include in the segment.
     * @return self
     */
    public static function offset(string|int $offset): self
    {
        return new self($offset, self::ARR_OFFSET_SEGMENT);
    }

    /**
     * Creates a new object property access segment with the offset passed in.
     * @param string $name The name of the object property to include in the segment.
     * @return self
     */
    public static function prop(string $name)
    {
        return new self($name, self::OBJ_PROPERTY_SEGMENT);
    }

    /**
     * Creates a new object method call access segment with the offset passed in.
     * @param string    $name       The name of the object method to include in the segment.
     * @param           ...$args    The arguments to call the function with.
     * @return self
     */
    public static function call(string $name, ...$args)
    {
        return new self([$name, ...$args], self::METHOD_CALL_SEGMENT);
    }
}
