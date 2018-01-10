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

    public function parent()
    {
        return (new Proxy($this, static::class))->parent();
    }

    public function getParentAttribute()
    {
        return (new Proxy($this, static::class))->parent;
    }

    public function getRootAttribute()
    {
        return (new Proxy($this, static::class))->root;
    }

    public function toTree()
    {
        $tree = $this->node->load('page')->toArray();
        $tree['children'] = $this->node->descendants->map->load('page')->toTree()->toArray();

        $toPage = function ($node) use (&$toPage) {
            $children = collect();

            if (isset($node['children'])) {
                foreach ($node['children'] as $child) {
                    $children[] = $toPage($child);
                }
            }

            // TODO: bug? no page bug node...
            $page = $node['node'];

            if (count($children)) {
                $page['children'] = $children;
            }

            return collect($page);
        };

        return $toPage($tree);
    }
}
