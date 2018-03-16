<?php

namespace UniSharp\UniCMS;

use Illuminate\Database\Eloquent\Model;
use UniSharp\UniCMS\Traits\Translatable;
use Illuminate\Database\Eloquent\Builder;

class Widget extends Model
{
    use Translatable;

    protected $fillable = ['type', 'sort'];

    protected $casts = [
        'sort' => 'integer',
        'data' => 'json',
    ];

    protected $translatedAttributes = ['data'];

    public static function boot()
    {
        parent::boot();

        static::addGlobalScope('sort', function (Builder $query) {
            return $query->orderBy('sort');
        });

        static::creating(function ($model) {
            $model->sort = $model->page->widgets()->count();

            return $model;
        });

        static::deleted(function ($model) {
            $model->page->widgets->each(function ($widget, $sort) {
                $widget->update(compact('sort'));
            });
        });
    }

    public function page()
    {
        return $this->morphTo('page');
    }
}
