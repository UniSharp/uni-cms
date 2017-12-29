<?php

namespace UniSharp\UniCMS;

class ParentStrategy
{
    protected $proxy;

    public function __construct(Proxy $proxy)
    {
        $this->proxy = $proxy;
    }

    public function __call($method, $args)
    {
        return $this->proxy->model->node->parent->page->$method(...$args);
    }
}
