<?php

namespace UniSharp\UniCMS;

class ParentStrategy extends BaseStrategy
{
    protected $proxy;

    public function __construct(Proxy $proxy)
    {
        $this->proxy = $proxy;
    }

    public function getResults()
    {
        return $this->proxy->model->node->parent->page;
    }
}
