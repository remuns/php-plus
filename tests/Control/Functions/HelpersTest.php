<?php

namespace PhpPlus\Core\Tests\Control\Functions;

use PhpPlus\Core\Tests\TestCase;

use Closure;
use PhpPlus\Core\Control\Functions\Helpers;

class HelpersTest extends TestCase
{
    /**
     * Tests the {@see Helpers::callableToClosure()} function when called on a non-Closure
     * callable.
     */
    public function testCallableToClosure()
    {
        $this->assertSame(
            16,
            self::closureAcceptor(Helpers::callableToClosure('self::squareTest'), 4));
    }
    private static function squareTest(int $i)
    {
        return $i * $i;
    }

    /**
     * Tests the {@see Helpers::callableToClosure()} function when called on a Closure.
     */
    public function testClosureToClosure()
    {
        $this->assertSame(
            0,
            self::closureAcceptor(Helpers::callableToClosure(fn (int $i) => $i - $i), 0));
    }

    private static function closureAcceptor(Closure $f, ...$args)
    {
        return call_user_func_array($f, $args);
    }
}
