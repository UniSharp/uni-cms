<?php

namespace UniSharp\UniCMS\Strategies;

use UniSharp\UniCMS\Proxy;

abstract class BaseStrategy
{
    protected $proxy;

    public function __construct(Proxy $proxy)
    {
        $this->proxy = $proxy;
    }

    abstract public function getResults();
}
