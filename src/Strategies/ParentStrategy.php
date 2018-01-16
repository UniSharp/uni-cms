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
        return $this->getRelation()->$method(...$args);
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
