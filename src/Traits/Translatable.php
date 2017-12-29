<?php

namespace UniSharp\UniCMS\Traits;

use UniSharp\UniCMS\Translation;
use Illuminate\Database\Eloquent\Builder;

trait Translatable
{
    protected $lang;

    protected $defaultLang = 'en';
    protected $fallbackLang = 'en';

    protected $translations = [];
    protected $originalTranslations = [];

    public static function bootTranslatable()
    {
        static::addGlobalScope('with', function (Builder $query) {
            return $query->with('translations');
        });

        static::created(function ($model) {
            $translations = [];

            foreach ($model->getTranslationDirty() as $lang => $trans) {
                foreach ($trans as $key => $value) {
                    $translations[] = compact('lang', 'key', 'value');
                }
            }

            $model->translations()->createMany($translations);
        });

        static::updated(function ($model) {
            foreach ($model->getTranslationDirty() as $lang => $trans) {
                foreach ($trans as $key => $value) {
                    $model->translations()->updateOrCreate(compact('lang', 'key'), compact('value'));

                    $model->setOriginalTranslation($lang, $key, $value);
                }
            }
        });

        static::deleted(function ($model) {
            $model->translations()->delete();
        });

        static::retrieved(function ($model) {
            foreach ($model->getRelationValue('translations') as $trans) {
                $model->setOriginalTranslation($trans->lang, $trans->key, $trans->value);
            }
        });
    }

    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    public function getTranslationDirty()
    {
        return $this->translations;
    }

    public function isTranslationDirty()
    {
        return count(array_flatten($this->getTranslationDirty())) > 0;
    }

    public function setOriginalTranslation($lang, $key, $value)
    {
        unset($this->translations[$lang][$key]);

        $this->originalTranslations[$lang][$key] = $value;

        return $this;
    }

    public function setTranslation($lang, $key, $value)
    {
        $this->translations[$lang][$key] = $value;

        return $this;
    }

    public function getTranslation($lang, $key)
    {
        return $this->translations[$lang][$key] ??
               $this->originalTranslations[$lang][$key] ??
               $this->translations[$this->fallbackLang][$key] ??
               $this->originalTranslations[$this->fallbackLang][$key] ??
               null;
    }

    public function translationsToArray($lang)
    {
        return array_merge(
            $this->originalTranslations[$this->fallbackLang] ?? [],
            $this->translations[$this->fallbackLang] ?? [],
            $this->originalTranslations[$lang] ?? [],
            $this->translations[$lang] ?? []
        );
    }

    public function translate($lang)
    {
        $this->lang = $lang;

        return $this;
    }

    public function getTranslatedAttributes()
    {
        return $this->translatedAttributes ?? [];
    }

    public function save(array $options = [])
    {
        if (!parent::save($options)) {
            return false;
        }

        if ($this->isTranslationDirty()) {
            $this->fireModelEvent('saved', false);
            $this->fireModelEvent('updated', false);
        }

        return true;
    }

    public function fill(array $attributes)
    {
        foreach ($this->translatableFromArray($attributes) as $key => $value) {
            $this->setTranslation($this->getLang(), $key, $value);
        }

        return parent::fill($attributes);
    }

    public function totallyGuarded()
    {
        return !!count($this->getTranslatedAttributes()) && parent::totallyGuarded();
    }

    public function __get($key)
    {
        if ($this->isTranslatedAttribute($key)) {
            return $this->getTranslation($this->getLang(), $key);
        }

        return parent::__get($key);
    }

    public function __set($key, $value)
    {
        if ($this->isTranslatedAttribute($key)) {
            $this->setTranslation($this->getLang(), $key, $value);

            return;
        }

        parent::__set($key, $value);
    }

    public function toArray()
    {
        $array = array_merge(
            $this->attributesToArray(),
            $this->relationsToArray(),
            $this->translationsToArray($this->getLang())
        );

        array_forget($array, 'translations');

        return $array;
    }

    protected function getLang()
    {
        return $this->lang ?: $this->defaultLang;
    }

    protected function isTranslatedAttribute($key)
    {
        return in_array($key, $this->getTranslatedAttributes());
    }

    protected function translatableFromArray(array $attributes)
    {
        return array_intersect_key($attributes, array_flip($this->getTranslatedAttributes()));
    }
}
