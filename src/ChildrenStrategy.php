<?php

namespace UniSharp\UniCMS;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class ChildrenStrategy
{
    protected $proxy;

    protected $isRelation;

    protected $relation;

    public function __construct(Proxy $proxy, $isRelation)
    {
        $this->proxy = $proxy;
        $this->isRelation = $isRelation;
    }

    public function create(array $attribute)
    {
        $instance = $this->proxy->class::create($attribute);

        $instance->node->appendToNode($this->proxy->model->node)->save();

        return $instance;
    }

    public function __call($method, $args)
    {
        if (!$this->relation) {
            $this->relation = $this->proxy->model->node->children();
        }

        if ($this->isRelation) {
            if ($method === 'first') {
                $result = $this->relation->$method(...$args);
            } else {
                $result = $this->relation->whereHas('page', function (Builder $query) use ($method, $args) {
                    return $query->$method(...$args);
                });
            }

            if ($result instanceof Relation) {
                $this->relation = $result;

                return $this;
            }

            if ($result instanceof Collection) {
                $result = $result->map;
            }

            return $result->page;
        }

        return $this->relation->getResults()->map->page->$method(...$args);
    }
}