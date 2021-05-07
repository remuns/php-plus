<?php

namespace PhpPlus\Core\Types;

use PhpPlus\Core\Traits\SingletonTrait;

final class TrueType extends BoolType
{
    use SingletonTrait;
    use SingleBoolTypeTrait;

    public function singleValue(): bool { return true; }

    public function __toString(): string { return 'true'; }
}
TrueType::__constructStatic();
