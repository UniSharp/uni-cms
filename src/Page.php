<?php

namespace UniSharp\UniCMS;

use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    protected $fillable = ['slug'];

    public function node()
    {
        return $this->morphOne(Node::class, 'node');
    }

    public function widgets()
    {
        return $this->morphMany(Widget::class, 'page');
    }
}
