<?php

namespace Flobbos\Crudable;

trait Crudable {
    
    protected $relation;
    
    /**
     * Get a single item or collection
     * @param int $id
     * @return Model/Collection
     */
    public function get($id = null){
        if(is_null($id)){
            return $this->find($id);
        }
        return $this->model->with($this->relation)->get();
    }
    
    /**
     * Alias of model find
     * @param int $id
     * @return Model
     */
    public function find($id){
        return $this->model->with($this->relation)->find($id);
    }
    
    /**
     * Retrieve single trashed item or all
     * @param int $id
     * @return Model/Collection
     */
    public function getTrash($id = null){
        if(is_null($id)){
            return $this->getTrashedItem($id);
        }
        return $this->model->onlyTrashed()->with($this->relation)->get();
    }
    
    /**
     * Return single trashed item
     * @param int $id
     * @return Model
     */
    public function getTrashedItem($id){
        return $this->model->withTrashed()->with($this->relation)->find($id);
    }
    
    /**
     * Set relationship for retrieving model and relations
     * @param array $relation
     * @return self
     */
    public function setRelation(array $relation){
        $this->relation = $relation;
        return $this;
    }
    
}