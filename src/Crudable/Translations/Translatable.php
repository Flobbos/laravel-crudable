<?php

namespace Flobbos\Crudable\Translations;

use Flobbos\Crudable\Exceptions\MissingTranslationsException;
use Flobbos\Crudable\Exceptions\MissingRequiredFieldsException;
use Flobbos\Crudable\Exceptions\MissingSlugFieldException;
use Flobbos\Crudable\Contracts\Sluggable;
use Illuminate\Support\Str;

trait Translatable{
    
    /**
     * If you custom named your translations you can set this here
     * @var string name of your translations 
     */
    protected $translation_name = 'translations';

    /**
     * Process translation input data for saving them.
     * @param array $translations
     * @param string $trans_key translation key field name
     * @param string $language_key language identifier field
     * @return array
     */
    public function processTranslations(
            array $translations, 
            string $trans_key = null, 
            string $language_key = 'language_id'){
        
        $approved = [];
        
        foreach($translations as $trans){
            //Check if translation is array and skip if not
            if(!is_array($trans)){
                continue;
            }
            //Check if translated slugs are used and validate against the DB
            if(config('crudable.translated_slugs') && isset($trans[config('crudable.slug_field_name')])){
                
            }
            //Check for translation key
            if(!is_null($trans_key)){
                unset($trans[$trans_key]);
            }
            if(!isset($this->required_trans) && !empty($this->filterNull($trans,$language_key))){
                $approved[] = $this->checkForSlug($trans);
            }
            elseif(isset($this->required_trans) && $this->checkRequired($trans)){
                $approved[] = $this->checkForSlug($trans);
            }
        }
        return $approved;
    }
    
    /**
     * Save translations to model
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param array $translations
     * @param string $relation_name
     * @return Model
     */
    public function saveTranslations(
            \Illuminate\Database\Eloquent\Model $model, 
            array $translations){
        
        if(empty($translations))
            throw new MissingTranslationsException;
        
        return $model->{$this->translation_name}()->saveMany($translations);
    }
    
    /**
     * If you set the required fields in your service class
     * you can check if these fields were set. 
     * @param array $arr
     * @return bool
     */
    public function checkRequired(array $arr){
        //Filter out null values
        $filtered = $this->filterNull($arr);
        
        if(!isset($this->required_trans))
            throw new MissingRequiredFieldsException;
        
        //check if all required fields are present
        return count(array_intersect_key(array_flip($this->required_trans), $filtered)) === count($this->required_trans);
    }
    
    /**
     * 
     * @param array $arr
     * @param string $except
     * @return type
     */
    public function filterNull(array $arr, string $except = null){
        if(is_null($except)){
            return array_filter($arr, function($var){
                return !is_null($var);
            });
        }
        if(!is_null($except)){
            $filtered = $this->filterNull($arr);
            if(isset($filtered[$except]) && count($filtered) == 1){
                return [];
            }
            return $filtered;
        }
    }
    
    /**
     * 
     * @param array $translations
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $translation_key
     * @param string $translation_class
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function updateTranslations(
            array $translations, 
            \Illuminate\Database\Eloquent\Model $model, 
            string $translation_key, 
            string $translation_class){
        
        //Update existing translations
        $remaining = [];
        foreach($translations as $trans){
            if(isset($trans[$translation_id]) && !is_null($trans[$translation_id])){
                $translation = $model->{$this->translation_name}()->where('id',$trans[$translation_id])->first();
                //Check if parent model is available
                if(isset($this->model)){
                    //Add keys that exist with deleted values
                    foreach($this->model->translatedAttributes as $key){
                        if(!array_key_exists($key, $trans)){
                            $trans[$key] = null;
                        }
                    }
                }
                //Delete translations, when empty data is received. 
                if(empty(array_intersect($this->model->translatedAttributes,array_keys($this->filterNull($trans))))){
                    $translation->delete();
                }
                else{
                    $translation->update($this->checkForSlug($trans));
                }
            }
            else{
                $remaining[] = $this->checkForSlug($trans);
            }
        }
        
        //Create new translations
        $new_translations = $this->processTranslations($remaining,$translation_id);
        if(!empty($new_translations)){
            $new_trans = [];
            foreach($new_translations as $n_t){
                $new_trans[] = new $translation_class($n_t);
            }
            $model->{$this->translation_name}()->saveMany($new_trans);
        }
        
        return $model;
    }
    
    /**
     * Generate URL slug from given string
     * @param string $name
     * @return string
     */
    public function generateSlug(string $name): string{
        return Str::slug($name);
    }
    
    private function checkForSlug(array $trans): array{
        //Don't use slugs
        if(!$this instanceof Sluggable){
            return $trans;
        }
        //Check if slug field is set
        if(!isset($this->slug_field)){
            throw new MissingSlugFieldException;
        }
        //Check if current translation contains a sluggable field
        if(array_key_exists($this->slug_field, $trans)){
            $trans[$this->slug_name ?? 'slug'] = $this->generateSlug($trans[$this->slug_field]);
        }
        return $trans;
    }
    
}