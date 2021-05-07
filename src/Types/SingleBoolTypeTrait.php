<?php

namespace PhpPlus\Core\Types;

/**
 * A trait for a type representing one of the single boolean values.
 */
trait SingleBoolTypeTrait
{
    use SingleValueTypeTrait;

    public function compare(Type $other): ?int
    {
        if ($other->typeIndicator() == Type::BOOLEAN_INDICATOR) {
            if ($other == BaseBoolType::value()) {
                return -1;
            } else {
                return $other->singleValue() === $this->singleValue() ? 0 : null;
            }
        } else {
            return $other->handleTrivialCompareCases();
        }
    }

    public abstract function singleValue(): bool;
}
