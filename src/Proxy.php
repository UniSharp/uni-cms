<?php

namespace UniSharp\UniCMS;

use Illuminate\Database\Eloquent\Model;
use UniSharp\UniCMS\Strategies\RootStrategy;
use UniSharp\UniCMS\Strategies\ParentStrategy;
use UniSharp\UniCMS\Strategies\ChildrenStrategy;

class Proxy
{
    public $model;

    public $class;

    protected $strategy;

    protected $strategies = [
        'root' => RootStrategy::class,
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
        if (array_key_exists($key, $this->strategies)) {
            return (new $this->strategies[$key]($this))->getResults();
        }
    }

    public function __call($method, $args)
    {
        if (array_key_exists($method, $this->strategies)) {
            $this->strategy = new $this->strategies[$method]($this);

            return $this;
        }

        return $this->strategy->$method(...$args);
    }
}
