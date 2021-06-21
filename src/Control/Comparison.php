<?php

namespace PhpPlus\Core\Control;

use PhpPlus\Core\Traits\StaticConstructibleTrait;
use PhpPlus\Core\Traits\WellDefinedSelf;

/**
 * A class representing a comparison between two objects.
 * 
 * @property-read bool $isLess
 *                          Whether or not this object represents a less-than comparison.
 * @property-read bool $isEqual
 *                          Whether or not this object represents an equal-to comparison.
 * @property-read bool $isGreater
 *                          Whether or not this object represents a greater-than comparison.
 * @property-read bool $isLessOrEqual
 *                          Whether or not this object represents a less-than or
 *                          equal-to comparison.
 * @property-read bool $isNotEqual
 *                          Whether or not this object represents a less-than or
 *                          greater-than comparison.
 * @property-read bool $isGreaterOrEqual
 *                          Whether or not this object represents a greater-than or
 *                          equal-to comparison.
 * @property-read int  $value
 *                          A numeric value corresponding to the result of a spaceship (`<=>`)
 *                          operator called on 2 arguments.
 *                          This value is `-1` for less-than comparisons, `0` for equal-to
 *                          comparisons, and `1` for greater-than comparisons.
 */
final class Comparison
{
    use WellDefinedSelf;
    use StaticConstructibleTrait;

    ///////////////////////////////////////////////////////
    /// Constants and Static Properties
    ///////////////////////////////////////////////////////
    private const GT = 1;
    private const EQ = 0;
    private const LT = -1;

    private static self $lt;
    private static self $eq;
    private static self $gt;
    ///////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////
    /// General magic methods
    ///////////////////////////////////////////////////////
    
    /**
     * Compares the left-hand and right-hand sides using this comparison. This method is an alias
     * for the compare method.
     * 
     * @param mixed $lhs    The left-hand side of the comparison to make.
     * @param mixed $rhs    The right-hand side of the comparison to make.
     * @param bool  $strict Whether or not to use strict equality comparison.
     * 
     * @return bool
     * 
     * @see self::compare
     * @see self::compareLoose
     * @see self::compareStrict

     */
    public function __invoke($lhs, $rhs, bool $strict = false): bool
    {
        return match ($this->comparisonValue) {
            self::GT => $lhs > $rhs,
            self::EQ => $strict ? $lhs === $rhs : $lhs == $rhs,
            self::LT => $lhs < $rhs,
        };
    }
    ///////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////
    /// Constructors
    ///////////////////////////////////////////////////////
    /**
     * Constructs a new instance of the comparison class.
     * This should only ever be called internally.
     * 
     * @internal
     * @param int $comparisonValue  The value of the comparison represented.
     *                              This should be one of the following:
     *                              * -1 (representing less than)
     *                              * 0 (representing equality)
     *                              * 1 (representing greater than)
     */
    private function __construct(private int $comparisonValue) { }

    public static function __initStatic(): void
    {
        self::$lt = new self(self::LT);
        self::$eq = new self(self::EQ);
        self::$gt = new self(self::GT);
    }
    ///////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////
    /// Getters
    ///////////////////////////////////////////////////////
    public function __get(string $name)
    {
        return match ($name) {
            // Boolean conversions
            'isLess' => $this->comparisonValue === self::LT,
            'isEqual' => $this->comparisonValue === self::EQ,
            'isGreater' => $this->comparisonValue === self::GT,
            'isLessOrEqual' => $this->comparisonValue !== self::GT,
            'isNotEqual' => $this->comparisonValue !== self::EQ,
            'isGreaterOrEqual' => $this->comparisonValue !== self::LT,

            // Wrapped numerical value
            'value' => $this->comparisonValue,
        };
    }
    ///////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////
    /// Compare
    ///////////////////////////////////////////////////////
    /**
     * Compares the left-hand and right-hand sides using this comparison.
     * 
     * @param mixed $lhs    The left-hand side of the comparison to make.
     * @param mixed $rhs    The right-hand side of the comparison to make.
     * 
     * @see compareLoose
     * @see compareStrict
     * 
     * @return bool
     */
    public function compare($lhs, $rhs): bool
    {
        return match ($this->comparisonValue) {
            self::GT => $lhs > $rhs,
            self::EQ => $lhs == $rhs,
            self::LT => $lhs < $rhs,
        };
    }

    ///////////////////////////////////////////////////////
    /// Linq-Like Methods
    ///////////////////////////////////////////////////////
    /**
     * Chooses one of the values passed in based on the type of comparison this is.
     * 
     * @param mixed $lt The value of the left-handed
     */
    public function choose($lt, $eq, $gt)
    {
        return match ($this->comparisonValue) {
            self::GT => $gt,
            self::EQ => $eq,
            self::LT => $lt,
        };
    }

    /**
     * Calls one of the functions passed in based on the type of comparison this is.
     * 
     * @param callable $lt The function to call in the less-than case.
     * @param callable $eq The function to call in the equal-to case.
     * @param callable $gt The function to call in the greater-than case.
     * 
     * @return mixed The result of calling the function indicated.
     */
    public function map(callable $lt, callable $eq, callable $gt)
    {
        return match ($this->comparisonValue) {
            self::GT => $gt(),
            self::EQ => $eq(),
            self::LT => $lt(),
        };
    }
    ///////////////////////////////////////////////////////

    ///////////////////////////////////////////////////////
    /// Factory Methods
    ///////////////////////////////////////////////////////
    /**
     * Calls the spaceship operator (<=>) on the arguments passed in, returning the result as
     * an instance of this class.
     * 
     * @param mixed $lhs The left-hand side of the comparison to make.
     * @param mixed $rhs The right-hand side of the comparison to make.
     * 
     * @return self
     */
    public static function spaceship($lhs, $rhs): self
    {
        $result = $lhs <=> $rhs;
        return match (true) {
            $result > 0 => self::$gt,
            $result < 0 => self::$lt,
            default => self::$eq,
        };
    }

    /**
     * Returns a comparison described by the result of applying the spaceship operator (`<=>`)
     * to a pair of operands.
     * 
     * @return self
     */
    public static function fromSpaceship(int $result): self
    {
        return match (true) {
            $result > 0 => self::$gt,
            $result < 0 => self::$lt,
            default => self::$eq,
        };
    }
    
    /**
     * Returns a comparison described by either
     * * Applying the spaceship operator (`<=>`) to a pair of operands
     * * Passing in `null`, representing two operands that do not compare.
     * 
     * This allows partial orders (where there are operands that do not compare) to be represented
     * by nullable instances of this class.
     */
    public static function fromNullableSpaceship(?int $result): ?self
    {
        return match (true) {
            $result === null => null,
            $result > 0 => self::$gt,
            $result < 0 => self::$lt,
            default => self::$eq,
        };
    }

    /**
     * Returns the "greater than" (`>`) comparison.
     * @return self
     */
    public static function greater() { return self::$gt; }

    /**
     * Returns the "equal to" (`==`/`===`) comparison.
     * @return self
     */
    public static function equal() { return self::$eq; }

    /**
     * Returns the "less than" (`<`) comparison.
     * @return self
     */
    public static function less() { return self::$lt; }
    ///////////////////////////////////////////////////////
} Comparison::__constructStatic();
