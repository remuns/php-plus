<?php

namespace PhpPlus\Core;

/**
 * Class Sample
 *
 * @author  Mahmoud Zalt  <mahmoud@zalt.me>
 */
class Sample
{

    /**
     * @var  \PhpPlus\Core\Config
     */
    private $config;

    /**
     * Sample constructor.
     *
     * @param \PhpPlus\Core\Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @param $name
     *
     * @return  string
     */
    public function sayHello($name)
    {
        $greeting = $this->config->get('greeting');

        return $greeting . ' ' . $name;
    }

}
