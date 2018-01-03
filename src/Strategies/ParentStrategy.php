<?php

namespace UniSharp\UniCMS\Strategies;

class ParentStrategy extends BaseStrategy
{
    protected $relation;

    public function getResults()
    {
        if ($this->proxy->model->node->parent) {
            return $this->proxy->model->node->parent->page;
        }

        return null;
    }

    public function __call($method, $args)
    {
        $result = $this->getRelation()->$method(...$args);

        if ($result instanceof Relation) {
            $this->relation = $result;

            return $this;
        }

        return $result;
    }

    protected function getRelation()
    {
        if (!$this->relation) {
            $this->relation = $this->proxy->model->node->parent()->where(
                'node_type',
                (new $this->proxy->class)->getMorphClass()
            );
        }

        return $this->relation;
    }
}
