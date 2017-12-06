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
   
    public static function boot()
    {
        parent::boot();

        static::deleted(function ($model) {
            $model->page->delete();
        });
    }
}
