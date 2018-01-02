<?php
namespace UniSharp\UniCMS\Strategies;

class ParentStrategy extends BaseStrategy
{
    public function getResults()
    {
        if ($this->proxy->model->node->parent) {
            return $this->proxy->model->node->parent->page;
        }
        
        return null;
    }
}
