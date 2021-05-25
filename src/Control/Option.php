<?php

namespace PhpPlus\Core\Control;

use InvalidArgumentException;
use PhpPlus\Core\Control\Access\Access;
use PhpPlus\Core\Control\Access\AccessSegment;
use PhpPlus\Core\Exceptions\InvalidOperationException;
use PhpPlus\Core\Traits\StaticConstructibleTrait;
use PhpPlus\Core\Traits\WellDefinedSelf;

/**
 * A class wrapping either a value or no value.
 * 
 * This provides a less ambiguous alternative to null, with additional methods for performing
 * useful operations.
 * 
 * @property-read mixed $fValue The value wrapped in this option, or `false` if the
 *                              option is empty.
 * @property-read bool  $isNone Whether or not this option is empty.
 * @property-read bool  $isSome Whether or not this option wraps a value.
 * @property-read mixed $nValue The value wrapped in this option, or `null` if the option is empty.
 * @property-read mixed $value  The value wrapped in this option.
 *                              Accessing this property will throw an exception if the option
 *                              is empty.
 */
final class Option
{
    use WellDefinedSelf;
    use StaticConstructibleTrait;

    /* *********************************************************** *
     * Static constructor and properties
     * *********************************************************** */
    private static self $none;

    protected static function __initStatic(): void
    {
        self::$none = new self(null, isSome: false);
    }

    /* *********************************************************** *
     * Constructors and simple factory methods
     * *********************************************************** */
    private function __construct(private $value, private bool $isSome) { }

    /**
     * Returns an empty option.
     * @return self
     */
    public static function none(): self { return self::$none; }

    /**
     * Returns an option wrapping the value passed in.
     * @param mixed $value The value to wrap.
     * @return self
     */
    public static function some($value): self { return new self($value, isSome: true); }

    /* *********************************************************** *
     * Basic getters
     * *********************************************************** */
    public function __get(string $name)
    {
        return match ($name) {
            // Wrapped value or false
            'fValue' => $this->isSome ? $this->value : false,

            'isNone' => !$this->isSome,
            'isSome' => $this->isSome,

            // Wrapped value or null
            'nValue' => $this->value,

            // Wrapped value or exception
            'value' =>
                $this->isSome ?
                    $this->value :
                    throw new InvalidOperationException('attempt to unwrap an empty option'),

            default =>
                throw new InvalidArgumentException("undefined property name '{$name}'"),
        };
    }

    /**
     * Gets the value wrapped in this option, or the default value passed in if the option
     * is empty.
     * @param mixed $default The value to return if the option is empty.
     * @return mixed 
     */
    public function valueOrDefault($default)
    {
        return $this->isSome ? $this->value : $default;
    }

    /**
     * Determines if this option wraps a value equal to the value passed in (using a loose
     * comparison).
     * 
     * @param $item The item to check for equality.
     * 
     * @return bool|null Whether or not the item was equal to the value wrapped in this option,
     *                   or null if the option was empty.
     */
    public function has($item): ?bool { return $this->isSome ? $this->value == $item : null; }

    /**
     * Determines if this option wraps the value passed in (using a strict comparison).
     * 
     * @param $item The item to check for.
     * 
     * @return bool|null Whether or not the item was the value wrapped in this option, or null
     *                   if the option was empty.
     */
    public function hasStrict($item): ?bool
    {
        return $this->isSome ? $this->value === $item : null;
    }

    /* *********************************************************** *
     * Functor methods
     * *********************************************************** */
    /**
     * Maps a callable over this option.
     * @param callable $f The callable to map over the option.
     * @return self
     */
    public function map(callable $f): self
    {
        return $this->isSome ? self::some($f($this->value)) : self::$none;
    }

    /**
     * Maps a series of callables over this option.
     * 
     * This may be more efficient (not to mention readable) than making repeated calls to
     * {@see self::map()} because this method avoids creating intermediate option values.
     * 
     * @param callable ...$funcs A param array of callables to map over the option.
     * 
     * @return self
     */
    public function mapAll(callable ...$funcs): self
    {
        if (empty($funcs)) { // Throw out empty cases without doing anything
            return $this;
        }

        // Apply all functions in order
        if ($this->isSome) {
            $val = $this->value;
            foreach ($funcs as $f) {
                $val = $f($val);
            }
            return self::some($val);
        }
        return self::$none;
    }

    /**
     * Allows a wrapped value to be accessed via access segment descriptions.
     * 
     * @param string|array|AccessSegment ...$accessSegments A list of access segments to use to
     *                                                      access the object.
     * 
     * @return self An option wrapping the result of the access, or Option::none() if the option
     *              passed in was empty.
     */
    public function access(string|array|AccessSegment ...$accessSegments): self
    {
        return $this->isSome ?
                self::some(Access::access($this->value, ...$accessSegments)) :
                self::$none;
    }

    /* *********************************************************** *
     * Applicative methods
     * *********************************************************** */
    /**
     * Applies the callable wrapped by this option to a list of arguments.
     * @param ...$params The list of parameters to apply the callable to.
     * @return self
     */
    public function apply(...$params): self
    {
        return $this->isSome ? self::some(($this->value)(...$params)) : self::$none;
    }

    /* *********************************************************** *
     * Monadic methods
     * *********************************************************** */
    /**
     * Binds a callable through this option.
     * @param callable $f A callable that should take in a single value and return an option.
     * @return self The result of applying the callable to the value wrapped in this option if it
     *              is nonempty, or Option::none() if it is empty.
     */
    public function bind(callable $f): self
    {
        return $this->isSome ? $f($this->value) : self::$none;
    }

    /**
     * Collapses values equivalent to false.
     * @return self The option passed in if that option did not wrap null, or Option::none()
     *              if it did.
     */
    public function collapseNull(): self
    {
        // No need to check isSome since none values wrap null internally and non-empty options
        // wrapping null are getting collapsed anyways
        return $this->value === null ? self::$none : $this;
    }

    /**
     * Collapses values equivalent to false.
     * @return self The option passed in if that option did not wrap a value equivalent to false,
     *              or Option::none() if it did.
     */
    public function collapseFalsy(): self
    {
        // No need to check isSome since none values wrap null internally (which is equivalent)
        // to false) and non-empty options wrapping falsy values are getting collapsed anyways
        return $this->value ? $this : self::$none;
    }

    /**
     * Allows a wrapped value to be accessed via access segment descriptions, collapsing null
     * values to Option::none().
     * 
     * @param string|array|AccessSegment ...$accessSegments A list of access segments to use to
     *                                                      access the object.
     * 
     * @return self An option wrapping the result of the access, or Option::none() if the option
     *              passed in was empty OR if a null value was encountered.
     * 
     * @see AccessSegment
     * @see Access
     */
    public function accessNullable(string|array|AccessSegment ...$accessSegments): self
    {
        if ($this->isSome) {
            // Perform the access
            $accessed = Access::accessNullable($this->value, ...$accessSegments);
            return $accessed === null ? self::$none : self::some($accessed);
        } else {
            return self::$none;
        }
    }

    /**
     * Allows a wrapped value to be accessed via access segment descriptions, collapsing values
     * equivalent to false to Option::none().
     * 
     * @param string|array|AccessSegment ...$accessSegments A list of access segments to use to
     *                                                      access the object.
     * 
     * @return self An option wrapping the result of the access, or Option::none() if the option
     *              passed in was empty OR if a value equivalent to false was encountered.
     * 
     * @see AccessSegment
     * @see Access
     */
    public function accessFalsy(string|array ...$accessSegments): self
    {
        if ($this->isSome) {
            // Type-check before allowing the procedure to continue
            $accessSegments = array_map(AccessSegment::class.'::create', $accessSegments);

            // Perform the access
            $newVal = $this->value;
            foreach ($accessSegments as $seg) {
                $newVal = Access::access($newVal, $seg);
                if (!$newVal) {
                    // Collapse falsy values immediately without evaluating the rest of the
                    // segment chain
                    return self::$none;
                }
            }
            return self::some($newVal);
        } else {
            return self::$none;
        }
    } 

    /* *********************************************************** *
     * Factory methods
     * *********************************************************** */
    /**
     * Returns an Option wrapping the value passed in, or Option::none() if the value passed in
     * is null.
     * @param $val The value to wrap.
     * @return self
     */
    public static function fromNullable($val): self
    {
        return $val === null ? self::$none : self::some($val);
    }

    /**
     * Returns an Option wrapping the value passed in, or Option::none() if the value passed in
     * is equivalent to false.
     * @param $val The value to wrap.
     * @return self
     */
    public static function fromFalsy($val): self
    {
        return (!$val) ? self::$none : self::some($val);
    }

    /**
     * Converts the nullable option passed in to a full option (collapsing null to Option::none()).
     * 
     * This method can be useful when taking in default options as parameters to methods; since
     * Option is a class and cannot be treated as a constant expression, a nullable option can be
     * included as the default and this function can be called to ensure that null is properly
     * collapsed into an Option instance before interaction with the parameter.
     * 
     * @param self|null $opt The nullable option to convert to an option.
     * 
     * @return self The option passed in, or Option::none() if null was passed in.
     */
    public static function fromNullableOption(?self $opt): self
    {
        return $opt === null ? self::$none : $opt;
    }
}
Option::__constructStatic();
