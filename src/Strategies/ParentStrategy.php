<?php

namespace UniSharp\UniCMS\Strategies;

class ParentStrategy extends BaseStrategy
{
    public function getResults()
    {
        return $this->proxy->model->node->parent->page;
    }
}
