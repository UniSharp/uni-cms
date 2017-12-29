<?php

namespace UniSharp\UniCMS;

use Illuminate\Database\Eloquent\Model;

class Proxy
{
    public $model;

    public $class;

    protected $strategy;

    protected $strategies = [
        'parent' => ParentStrategy::class,
        'children' => ChildrenStrategy::class,
    ];

    public function __construct(Model $model, $class)
    {
        $this->model = $model;
        $this->class = $class;
    }

    public function __get($key)
    {
        if (in_array($key, ['children', 'parent'])) {
            return (new $this->strategies[$key]($this))->getResults();
        }
    }

    public function __call($method, $args)
    {
        if (in_array($method, ['children', 'parent'])) {
            $this->strategy = new $this->strategies[$method]($this);

            return $this;
        }

        return $this->strategy->$method(...$args);
    }
}
