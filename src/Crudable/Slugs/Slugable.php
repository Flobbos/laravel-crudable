<?php

namespace Flobbos\Crudable\Translations;

use Flobbos\Crudable\Exceptions\SlugNotFoundException;

trait Slugable {
    
    public function getResourceIdFromTranslatedSlug(string $slug, string $related_id): int{
        if($model = $this->model->translations()->select($related_id)->where('slug',$slug)->first()){
            return $model->area_id;
        }
        throw new SlugNotFoundException;
    }
    
    public function getTranslatedSlugFromResourceId(int $id, string $related_id, int $language_id = null): string{
        if($model = $this->model->translations()->select('slug')->where($related_id,$id)->where('language_id',$language_id)->first()){
            return $model->slug;
        }
        return '';
    }
    
    public function getResourceIdFromSlug(string $slug): int{
        if($model = $this->model->select('id')->where('slug',$slug)->first()){
            return $model->id;
        }
        throw new SlugNotFoundException;
    }
    
    public function getSlugFromResourceId(int $id): string{
        if($model = $this->model->select('slug')->find($id)){
            return $model->slug;
        }
        return '';
    }
    
}