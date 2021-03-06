<?php

namespace Flobbos\Crudable\Contracts;

interface Crud
{

    /**
     * Return the Eloquent model from the service
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function raw();

    /**
     * Include your where statement here with variable parameters
     * @param mixed $params
     */
    public function where(...$params);

    /**
     * Get a single item or a collection of items.
     * Alias of find method when used with ID
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Support\Collection|null
     */
    public function get($id = null);

    /**
     * Returns the first row of the selected resource
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function first();

    /**
     * Get a single item
     * @param int $id 
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function find($id);

    /**
     * Paginate collection result
     * @param int $perPage defines the number of items per page
     */
    public function paginate($perPage);

    /**
     * Get single item or all items from trash if ID is null
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model|\Illuminate\Support\Collection
     */
    public function getTrash($id = null);

    /**
     * Get single trashed item
     * @param int $id
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getTrashedItem($id);

    /**
     * Set the related data that should be eager loaded
     * @param array $relation
     * @return self
     */
    public function setRelation(array $relation);

    /**
     * Synonymous for setRelation but accepts strings as well as arrays
     * @param string|array $relations
     * @return self
     */
    public function with($relations);
    /**
     * Use ordering in your query
     * @param string $field ordering field
     * @param string $order ordering direction asc is default
     */
    public function orderBy($field, $order = 'asc');

    /**
     * Create new entry 
     * @param array $data
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function create(array $data);

    /**
     * Update model. Make sure fillable is set on the model
     * @param int $id of model you want to update
     * @param array $data of model data that should be updated
     * @param bool $return_model set to true if you need a model instance back
     * @return bool|\Illuminate\Database\Eloquent\Model
     */
    public function update($id, array $data, $return_model = false);

    /**
     * Delete item either by softdelete or harddelete
     * @param int $id
     * @param bool $hardDelete
     * @return bool
     */
    public function delete($id, $hardDelete = false);

    /**
     * Restore a soft deleted model
     * @param int $id
     * @return bool
     */
    public function restore($id);

    /**
     * Set hasMany relationship by adding the related model, data and 
     * relation name
     * @param array $data
     * @param string $relatedModel
     * @param string $relation
     * @return self
     */
    public function withHasMany(array $data, $relatedModel, $relation);

    /**
     * Add the belongsToMany relationship data to be synced and define
     * the relationship name
     * @param array $data
     * @param string $relation
     * @return self
     */
    public function withBelongsToMany(array $data, $relation);

    /**
     * Handle a file or photo upload
     * @param \Illuminate\Http\Request $request
     * @param string $field_name upload field name
     * @param string $folder storage folder
     * @param string $storage_disk storage disk to be used
     * @param bool $randomize to randomize the filename
     * @return string filename
     */
    public function handleUpload(\Illuminate\Http\Request $request, $field_name = 'photo', $folder = 'images', $storage_disk = 'public', $randomize = true);

    /**
     * Handle uploaded file object
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $folder
     * @param string $storage_disk
     * @param boolean $randomize
     * @return string $filename
     */
    public function handleUploadedFile(\Illuminate\Http\UploadedFile $file, string $folder = 'images', string $storage_disk = 'public', bool $randomize = true);
}
