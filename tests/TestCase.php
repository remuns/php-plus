<?php

namespace PhpPlus\Core\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

/**
 * Class TestCase
 *
 * @author  Mahmoud Zalt  <mahmoud@zalt.me>
 */
class TestCase extends BaseTestCase
{

    public function __construct()
    {
        parent::__construct();
    }

    public function setUp(): void
    {
        parent::setUp();
    }

    public function tearDown(): void
    {
        parent::tearDown();
    }

}
