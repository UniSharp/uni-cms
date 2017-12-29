<?php

namespace UniSharp\UniCMS;

use Illuminate\Database\Eloquent\Model;

class Proxy
{
    public $model;

    public $class;

    protected $strategy;

    public function __construct(Model $model, $class)
    {
        $this->model = $model;
        $this->class = $class;
    }

    public function children($isRelation = true)
    {
        $this->strategy = new ChildrenStrategy($this, $isRelation);

        return $this;
    }

    public function parent()
    {
        $this->strategy = new ParentStrategy($this);

        return $this;
    }

    public function __call($method, $args)
    {
        return $this->strategy->$method(...$args);
    }
}
