<?php

namespace UniSharp\UniCMS;

use Illuminate\Database\Eloquent\Model;
use UniSharp\UniCMS\Traits\Translatable;

class Page extends Model
{
    use Translatable;

    protected $fillable = ['slug', 'published_at'];

    protected $translatedAttributes = ['name'];

    public function node()
    {
        return $this->morphOne(Node::class, 'node');
    }

    public function widgets()
    {
        return $this->morphMany(Widget::class, 'page');
    }
}
