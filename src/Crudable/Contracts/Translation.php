<?php

namespace Flobbos\Crudable\Contracts;

interface Translation {
    
    /**
     * Process translations array from form input
     * @param array $translations
     * @param null/string $trans_key
     * @param string $language_key
     * @return array
     */
    public function processTranslations(
            array $translations, 
            $trans_key = null, 
            $language_key = 'language_id');
    
    /**
     * Save translations to model
     * @param \Illuminate\Database\Eloquent\Model $model
     * @param array $translations
     * @return Model
     */
    public function saveTranslations(
            \Illuminate\Database\Eloquent\Model $model, 
            array $translations);
    
    /**
     * Update existing translations from form input
     * @param array $translations from form
     * @param \Illuminate\Database\Eloquent\Model $model existing model
     * @param string $translation_key name of translation key field 
     * @param string $translation_class name of related translation class
     * @return Model
     */
    public function updateTranslations(
            array $translations, 
            \Illuminate\Database\Eloquent\Model $model, 
            $translation_key, 
            $translation_class);
    
    /**
     * If you set the required fields in your service class
     * you can check if these fields were set. 
     * @param array $arr
     * @return bool
     */
    public function checkRequired(array $arr);
    
    /**
     * Filter fields with null input
     * @param array $arr
     * @param type $except
     * @return array
     */
    public function filterNull(array $arr, $except = null);
    
}