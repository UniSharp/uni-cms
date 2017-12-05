<?php

namespace UniSharp\UniCMS;

use Illuminate\Database\Eloquent\Model;

class Translation extends Model
{
    protected $fillable = ['lang', 'key', 'value'];

    protected $casts = ['value' => 'json'];
}
