<?php

namespace UniSharp\UniCMS;

use Kalnoy\Nestedset\NodeTrait;
use Illuminate\Database\Eloquent\Model;

class Node extends Model
{
    use NodeTrait;

    public static function boot()
    {
        parent::boot();

        static::deleted(function ($model) {
            $model->page->delete();
        });
    }

    public function page()
    {
        return $this->morphTo('page', 'node_type', 'node_id');
    }
}
