<?php

namespace PhpPlus\Core\Types;

use PhpPlus\Core\Traits\SingletonTrait;

final class FalseType extends BoolType
{
    use SingletonTrait;
    use SingleBoolTypeTrait;

    public function singleValue(): bool { return false; }

    public function __toString(): string { return 'false'; }
}
FalseType::__constructStatic();
