<?php

namespace Flobbos\Crudable;

use Exception;
use Cocur\Slugify\Slugify;
use Illuminate\Support\Str;
use Flobbos\Crudable\Contracts\Sluggable;
use Flobbos\Crudable\Exceptions\MissingSlugFieldException;
use Flobbos\Crudable\Exceptions\MissingRelationDataException;

trait Crudable
{

    protected   $relation = [];
    protected   $withHasMany, $withBelongsToMany, $model;

    /**
     * Retrieve the Eloquent model
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function raw()
    {
        return $this->model;
    }

    /**
     * Get a single item or collection
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Support\Collection
     */
    public function get($id = null)
    {
        if (!is_null($id)) {
            return $this->find($id);
        }
        return $this->model->get();
    }

    /**
     * Returns the first row of the selected resource
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function first()
    {
        return $this->model->first();
    }

    /**
     * Adds a chainable where statement
     * @param array|mixed $params
     * @return $this self
     */
    public function where(...$params)
    {
        $this->model = $this->model->where(...$params);
        return $this;
    }
    /**
     * Get paginated collection
     * @param int $perPage
     * @return Collection
     */
    public function paginate($perPage)
    {
        return $this->model->paginate($perPage);
    }

    /**
     * Alias of model find
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function find($id)
    {
        return $this->model->find($id);
    }

    /**
     * Retrieve single trashed item or all
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Support\Collection
     */
    public function getTrash($id = null)
    {
        if (!is_null($id)) {
            return $this->getTrashedItem($id);
        }
        return $this->model->onlyTrashed()->get();
    }

    /**
     * Return single trashed item
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getTrashedItem($id)
    {
        return $this->model->withTrashed()->find($id);
    }

    /**
     * Set relationship for retrieving model and relations
     * @param array $relation
     * @return self
     */
    public function setRelation(array $relation)
    {
        $this->model = $this->model->with($relation);
        return $this;
    }

    /**
     * Same as setRelation but accepts strings and arrays
     * @param string|array $relations
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function with($relations)
    {
        return $this->setRelation(is_string($relations) ? func_get_args() : $relations);
    }

    /**
     * Order the collection you pull
     * @param string $field
     * @param string $order default asc
     * @return self
     */
    public function orderBy($field, $order = 'asc')
    {
        $this->model = $this->model->orderBy(...func_get_args());
        return $this;
    }

    /**
     * Create new database entry including related models
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data)
    {
        $model = $this->model->create($this->checkForSlug($data));
        //check for hasMany
        if ($this->validateRelationData($this->withHasMany, 'many')) {
            $model->{$this->withHasMany['relation']}()->saveMany($this->withHasMany['data']);
        }
        //check for belongsToMany
        if ($this->validateRelationData($this->withBelongsToMany, 'tomany')) {
            $model->{$this->withBelongsToMany['relation']}()->sync($this->withBelongsToMany['data']);
        }
        return $model;
    }

    /**
     * Update Model
     * @param array $data
     * @return bool|\Illuminate\Database\Eloquent\Model
     */
    public function update($id, array $data, $return_model = false)
    {
        $model = $this->find($id);
        if ($return_model) {
            $model->update($this->checkForSlug($data));
            return $model;
        }
        return $model->update($this->checkForSlug($data));
    }

    /**
     * Delete model either soft or hard delete
     * @param int $id
     * @param bool $hardDelete
     * @return bool
     */
    public function delete($id, $hardDelete = false)
    {
        if ($hardDelete) {
            return $this->model->withTrashed()->find($id)->forceDelete($id);
        }
        return $this->model->find($id)->delete($id);
    }

    /**
     * Restore a previously soft deleted model
     * @param int $id
     * @return bool
     */
    public function restore($id)
    {
        return $this->model->withTrashed()->find($id)->restore();
    }

    /**
     * Set related models that need to be created
     * for a hasMany relationship
     * @param array $data
     * @param string $relatedModel
     * @return self
     */
    public function withHasMany(array $data, $relatedModel, $relation_name)
    {
        $this->withHasMany['relation'] = $relation_name;
        foreach ($data as $k => $v) {
            $this->withHasMany['data'][] = new $relatedModel($v);
        }
        return $this;
    }

    /**
     * Set related models for belongsToMany relationship
     * @param array $data
     * @return self
     */
    public function withBelongsToMany(array $data, $relation)
    {
        $this->withBelongsToMany = [
            'data' => $data,
            'relation' => $relation
        ];
        return $this;
    }

    /**
     * Handle a file upload
     * @param \Illuminate\Http\Request $request
     * @param string $fieldname
     * @param string $folder
     * @param string $storage_disk
     * @return string filename
     */
    public function handleUpload(\Illuminate\Http\Request $request, $fieldname = 'photo', $folder = 'images', $storage_disk = 'public', $randomize = true)
    {
        if (is_null($request->file($fieldname)) || !$request->file($fieldname)->isValid()) {
            throw new Exception(trans('crud.invalid_file_upload'));
        }
        //Get filename
        $basename = basename($request->file($fieldname)->getClientOriginalName(), '.' . $request->file($fieldname)->getClientOriginalExtension());
        if ($randomize) {
            $filename = Str::slug($basename) . '_' . uniqid() . '.' . $request->file($fieldname)->getClientOriginalExtension();
        } else {
            $filename = Str::slug($basename) . '.' . $request->file($fieldname)->getClientOriginalExtension();
        }
        //Move file to location
        $request->file($fieldname)->storeAs($folder, $filename, $storage_disk);
        return $filename;
    }

    /**
     * Handle uploaded file object
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $folder
     * @param string $storage_disk
     * @param boolean $randomize
     * @return string $filename
     */
    public function handleUploadedFile(\Illuminate\Http\UploadedFile $file, string $folder = 'images', string $storage_disk = 'public', bool $randomize = true)
    {
        //Get filename
        $basename = basename($file->getClientOriginalName(), '.' . $file->getClientOriginalExtension());
        if ($randomize) {
            $filename = Str::slug($basename) . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        } else {
            $filename = Str::slug($basename) . '.' . $file->getClientOriginalExtension();
        }
        //Move file to location
        $file->storeAs($folder, $filename, $storage_disk);
        return $filename;
    }

    private function validateRelationData($related_data, $type)
    {
        //Check if data attribute was set
        if (!is_null($this->withHasMany) && $type == 'many') {
            if (!isset($this->withHasMany['relation']) || !isset($this->withHasMany['data']))
                throw new MissingRelationDataException('HasMany Relation');
            return true;
        }
        if (!is_null($this->withBelongsToMany) && $type == 'tomany') {
            if (!isset($this->withBelongsToMany['relation']) || !isset($this->withBelongsToMany['data']))
                throw new MissingRelationDataException('BelongsToMany Relation');
            return true;
        }
        return false;
    }

    /**
     * Generate URL slug from given string
     * @param string $name
     * @return string
     */
    public function generateSlug(string $name): string
    {
        if (config('crudable.localized_slugs')) {
            $slugify = new Slugify();
            $slugify->activateRuleSet(config('crudable.localization_rule'));
            return $slugify->slugify($name);
        }
        return Str::slug($name);
    }

    private function checkForSlug(array $data): array
    {
        //Don't use slugs
        if (!$this instanceof Sluggable) {
            return $data;
        }
        //Check if slug field is set
        if (!isset($this->slug_field)) {
            throw new MissingSlugFieldException('The slug_field is required');
        }
        //Check if current translation contains a sluggable field
        if (array_key_exists($this->slug_field, $data)) {
            $data[$this->slug_name ?? 'slug'] = $this->generateSlug($data[$this->slug_field]);
        }
        return $data;
    }
}
