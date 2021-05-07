<?php

namespace PhpPlus\Core\Types;

/**
 * A trait for all types that are not the trivial top (anything) or bottom (nothing) type.
 */
trait NonTrivialTypeTrait
{
    public function handleTrivialCompareCases(): ?int { return null; }
}
