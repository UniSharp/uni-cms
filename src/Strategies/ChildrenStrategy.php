<?php

namespace UniSharp\UniCMS\Strategies;

use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;

class ChildrenStrategy extends BaseStrategy
{
    protected $relation;

    public function create(array $attribute)
    {
        $instance = $this->proxy->class::create($attribute);

        $instance->node->appendToNode($this->proxy->model->node)->save();

        return $instance;
    }

    public function getResults()
    {
        return $this->getRelation()->getResults()->map->page;
    }

    public function __call($method, $args)
    {
        if (in_array($method, ['get', 'first'])) {
            $result = $this->getRelation()->$method(...$args);
        } else {
            $result = $this->getRelation()->whereHas('page', function (Builder $query) use ($method, $args) {
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

    protected function getRelation()
    {
        if (!$this->relation) {
            $this->relation = $this->proxy->model->node->children()->where(
                'node_type',
                (new $this->proxy->class)->getMorphClass()
            );
        }

        return $this->relation;
    }
}
