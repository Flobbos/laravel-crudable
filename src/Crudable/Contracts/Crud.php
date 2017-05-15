<?php

namespace Flobbos\Crudable\Contracts;

interface Crud {
    
    /**
     * Get a single item or a collection of items.
     * Alias of find method when used with ID
     * @param int $id
     */
    public function get($id = null);
    
    /**
     * Get a single item
     * @param int $id 
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
     */
    public function getTrash($id = null);
    
    /**
     * Get single trashed item
     * @param int $id
     */
    public function getTrashedItem($id);
    
    /**
     * Set the related data that should be eager loaded
     * @param array $relation
     */
    public function setRelation(array $relation);
    
    /**
     * Create new entry with optional related data
     * @param array $data
     * @param string $relationName
     */
    public function create(array $data, $relationName = null);
    
    /**
     * Update model. make sure fillable is set on the model
     * @param array $data
     */
    public function udpate($id,array $data);
    
    /**
     * Delete item either by softdelete or harddelete
     * @param int $id
     * @param bool $hardDelete
     */
    public function delete($id, $hardDelete = false);
}