<?php

namespace UniSharp\UniCMS\Strategies;

class RootStrategy extends BaseStrategy
{
    public function getResults()
    {
        $walker = &$this->proxy->model->node->parent;

        while ($walker->parent) {
            $walker = $walker->parent;
        }

        return $walker->page;
    }
}
