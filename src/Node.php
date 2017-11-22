<?php

namespace UniSharp\UniCMS;

use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    use NodeTrait;

    public function page()
    {
        return $this->morphTo('node');
    }
}
