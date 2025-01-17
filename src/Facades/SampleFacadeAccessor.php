<?php

namespace PhpPlus\Core\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class SampleFacadeAccessor
 *
 * @author  Mahmoud Zalt  <mahmoud@zalt.me>
 */
class SampleFacadeAccessor extends Facade
{

    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'phpplus.core.sample';
    }
}
