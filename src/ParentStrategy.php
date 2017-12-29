<?php

namespace UniSharp\UniCMS;

class ParentStrategy
{
    protected $adapter;

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
    }

    public function __call($method, $args)
    {
        return $this->adapter->model->node->parent->page->$method(...$args);
    }
}
