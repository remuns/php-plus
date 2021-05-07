<?php

namespace PhpPlus\Core\Types;

/**
 * A trait for types that only contain a single value (like the 'false' type).
 */
trait SingleValueTypeTrait
{
    public function has($item): bool { return $item === $this->singleValue(); }

    /**
     * Gets the single value that this type represents.
     * @return mixed
     */
    public abstract function singleValue(): mixed;
}
