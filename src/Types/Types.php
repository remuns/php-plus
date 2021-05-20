<?php

namespace PhpPlus\Core\Types;

use PhpPlus\Core\Traits\StaticClassTrait;
use PhpPlus\Core\Traits\StaticConstructibleTrait;

final class Types
{
    use StaticClassTrait;
    use StaticConstructibleTrait;

    private static Type $meta;

    protected static function __initStatic(): void
    {
        self::$meta = new ClassType(Type::class);
    }

    /**
     * Gets a type representing the {@see Type} class.
     * @return Type
     */
    public static function meta(): Type { return self::$meta; }
} Types::__constructStatic();
