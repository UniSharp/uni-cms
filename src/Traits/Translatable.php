<?php

namespace UniSharp\UniCMS\Traits;

use UniSharp\UniCMS\Translation;
use Illuminate\Support\Facades\Lang;
use Illuminate\Database\Eloquent\Builder;

trait Translatable
{
    protected $lang;

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
                    if (!is_null($value)) {
                        $translations[] = compact('lang', 'key', 'value');

                        $model->setOriginalTranslation($lang, $key, $value);
                    } else {
                        $model->unsetTranslation($lang, $key);
                    }
                }
            }

            $model->translations()->createMany($translations);
        });

        static::updated(function ($model) {
            foreach ($model->getTranslationDirty() as $lang => $trans) {
                foreach ($trans as $key => $value) {
                    if (!is_null($value)) {
                        $model->translations()->updateOrCreate(compact('lang', 'key'), compact('value'));

                        $model->setOriginalTranslation($lang, $key, $value);
                    } else {
                        $model->translations()->where(compact('lang', 'key'))->delete();

                        $model->unsetTranslation($lang, $key);
                    }
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
        if ($this->isJsonCastable($key) && ! is_null($value)) {
            $value = $this->castAttributeAsJson($key, $value);
        }

        $this->translations[$lang][$key] = $value;

        return $this;
    }

    public function unsetTranslation($lang, $key)
    {
        unset($this->translations[$lang][$key]);

        return $this;
    }

    public function getTranslation($lang, $key)
    {
        $value = $this->translations[$lang][$key] ??
                 $this->originalTranslations[$lang][$key] ??
                 $this->translations[Lang::getFallback()][$key] ??
                 $this->originalTranslations[Lang::getFallback()][$key] ??
                 null;

        if ($this->hasCast($key)) {
            $value = $this->castAttribute($key, $value);
        }

        return $value;
    }

    public function translationsToArray($lang)
    {
        return $this->addCastAttributesToArray(array_merge(
            $this->originalTranslations[Lang::getFallback()] ?? [],
            $this->translations[Lang::getFallback()] ?? [],
            $this->originalTranslations[$lang] ?? [],
            $this->translations[$lang] ?? []
        ), []);
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
        $isUpdate = $this->exists;

        if (!parent::save($options)) {
            return false;
        }

        if ($isUpdate && $this->isTranslationDirty()) {
            $this->fireModelEvent('saved', false);
            $this->fireModelEvent('updated', false);
        }

        return true;
    }

    public function fill(array $attributes)
    {
        foreach ($this->translatedFromArray($attributes) as $key => $value) {
            $this->setTranslation($this->getLang(), $key, $value);

            array_forget($attributes, $key);
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
            array_fill_keys($this->getTranslatedAttributes(), null),
            $this->translationsToArray($this->getLang())
        );

        array_forget($array, 'translations');

        return $array;
    }

    protected function getLang()
    {
        return $this->lang ?: Lang::getLocale();
    }

    protected function isTranslatedAttribute($key)
    {
        return in_array($key, $this->getTranslatedAttributes());
    }

    protected function translatedFromArray(array $attributes)
    {
        return array_intersect_key($attributes, array_flip($this->getTranslatedAttributes()));
    }
}
