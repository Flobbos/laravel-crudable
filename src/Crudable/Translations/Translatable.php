<?php

namespace Flobbos\Crudable\Translations;

use Flobbos\Crudable\Exceptions\MissingTranslationsException;
use Flobbos\Crudable\Exceptions\MissingRequiredFieldsException;

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
            $trans_key = null, 
            $language_key = 'language_id'){
        
        $approved = [];
        
        foreach($translations as $trans){
            //Check if translation is array at and skip
            if(!is_array($trans)){
                continue;
            }
            //Check for translation key
            if(!is_null($trans_key)){
                unset($trans[$trans_key]);
            }
            if(!isset($this->required_trans) && !empty($this->filterNull($trans,$language_key))){
                $approved[] = $trans;
            }
            elseif(isset($this->required_trans) && $this->checkRequired($trans)){
                $approved[] = $trans;
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
        
        if(isset($this->required_trans))
            throw new MissingRequiredFieldsException;
        
        //check if all required fields are present
        return count(array_intersect_key(array_flip($this->required_trans), $filtered)) === count($this->required_trans);
    }
    
    /**
     * 
     * @param array $arr
     * @param type $except
     * @return type
     */
    public function filterNull(array $arr, $except = null){
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
     * @param type $translations
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param string $translation_id
     * @param type $translation_class
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function updateTranslations(
            array $translations, 
            \Illuminate\Database\Eloquent\Model $model, 
            $translation_id, 
            $translation_class){
        
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
                    $translation->update($trans);
                }
            }
            else{
                $remaining[] = $trans;
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
    
}