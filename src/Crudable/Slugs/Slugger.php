<?php

namespace Flobbos\Crudable\Translations;

use Flobbos\Crudable\Translations\TranslationReflector;
use Flobbos\Crudable\Exceptions\SlugNotFoundException;

trait Slugger
{

    public function getResourceIdFromTranslatedSlug(string $slug): int
    {
        // First we try to create a reflection of the currently used Model
        $reflector = new TranslationReflector($this->model);
        //Get the translation class from reflector
        $translation_class = $reflector->translationClass();
        //Find the content by the given slug
        if ($model = $translation_class->select($reflector->foreignKeyName())->where($this->slug_name ?? 'slug', $slug)->first()) {
            return $model->{$reflector->foreignKeyName()};
        }
        throw new SlugNotFoundException($slug . ' does not exist.');
    }

    public function getTranslatedSlugFromResourceId(int $id, int $language_id): string
    {
        //Get reflector first
        $reflector = new TranslationReflector($this->model);
        //Get the translation class from reflector
        $translation_class = $reflector->translationClass();
        //Find the slug by the given resource ID and language ID
        if ($model = $translation_class->select('slug')->where($reflector->foreignKeyName(), $id)->where('language_id', $language_id)->first()) {
            return $model->slug;
        }
        throw new SlugNotFoundException($id . ' does not have a corresponding slug');
    }

    public function getResourceIdFromSlug(string $slug): int
    {
        if ($model = $this->model->select('id')->where('slug', $slug)->first()) {
            return $model->id;
        }
        throw new SlugNotFoundException;
    }

    public function getSlugFromResourceId(int $id): string
    {
        if ($model = $this->model->select('slug')->find($id)) {
            return $model->slug;
        }
        throw new SlugNotFoundException($id . ' does not have a corresponding slug');
    }
}
