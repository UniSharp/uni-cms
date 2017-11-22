<?php

namespace UniSharp\UniCMS;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Widget extends Model
{
    protected $casts = [
        'sort' => 'integer',
    ];

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('sort', function (Builder $query) {
            return $query->orderBy('sort');
        });

        static::creating(function ($model) {
            $model->sort = $model->page->widgets->count();

            return $model;
        });
    }

    public function page()
    {
        return $this->morphTo('page');
    }
}
