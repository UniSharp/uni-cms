<?php

namespace UniSharp\UniCMS;

use Illuminate\Database\Eloquent\Model;
use UniSharp\UniCMS\Traits\Translatable;

class Page extends Model
{
    use Translatable;

    protected $fillable = ['slug', 'published_at'];

    protected $translatedAttributes = ['name'];

    public static function boot()
    {
        parent::boot();

        static::created(function (Page $page) {
            $node = new Node;

            $node->page()->associate($page);

            $node->save();
        });
    }

    public function node()
    {
        return $this->morphOne(Node::class, 'node');
    }

    public function widgets()
    {
        return $this->morphMany(Widget::class, 'page');
    }

    public function children()
    {
        return (new Proxy($this, static::class))->children();
    }

    public function getChildrenAttribute()
    {
        return (new Proxy($this, static::class))->children;
    }

    public function getParentAttribute()
    {
        return (new Proxy($this, static::class))->parent;
    }

    public function getRootAttribute()
    {
        return (new Proxy($this, static::class))->root;
    }
}
