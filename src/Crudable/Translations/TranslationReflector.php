<?php

namespace Flobbos\Crudable\Translations;

use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Database\Eloquent\Model;
use ReflectionClass;
use ReflectionException;

class TranslationReflector
{
    protected $reflector;

    public function __construct(Model $model)
    {
        $this->reflectOn($model);
    }

    /**
     * Create a reflection class of the given Eloquent model
     *
     * @param Model $model
     * @return void
     */
    private function reflectOn(Model $model)
    {
        try {
            $this->reflector = new ReflectionClass($model);
        } catch (ReflectionException $ex) {
            throw new BindingResolutionException('Target class does not exist.', 0, $ex);
        }
    }

    /**
     * Get the translation class from given reflection class
     *
     * @return Model
     */
    public function translationClass(): Model
    {
        $translation_class = $this->reflector->getName() . 'Translation';
        if (!class_exists($translation_class)) {
            throw new BindingResolutionException($translation_class . ' does not exist.');
        }
        return new $translation_class;
    }

    /**
     * Get the foreign key name from the given translation class
     *
     * @return string
     */
    public function foreignKeyName(): string
    {
        return strtolower($this->reflector->getShortName()) . '_id';
    }
}
