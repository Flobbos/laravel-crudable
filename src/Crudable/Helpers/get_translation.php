<?php

if (!function_exists('get_translation')) {
    function get_translation($collection,$key,$lang_id = 1,$array = false){
        $object = $collection->where('language_id',$lang_id)->first();
        if(!is_null($object) && isset($object->{$key})){
            return $object->{$key};
        }
        
        if($array){
            return [];
        }
        return null;
    }
}
