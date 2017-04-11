<?php

namespace Flobbos\Crudable;

trait Crudable {
    
    protected $relation = [];
    protected $withHasMany,$withBelongsToMany;
    /**
     * Get a single item or collection
     * @param int $id
     * @return Model/Collection
     */
    public function get($id = null){
        if(!is_null($id)){
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
        if(!is_null($id)){
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
    
    public function create(array $data, $relationName = null){
        $model = $this->model->create($data);
        //check for hasMany
        if(!is_null($this->withHasMany) && !is_null($relationName)){
            $model->{$relationName}()->saveMany($this->withHasMany);
        }
        //check for belongsToMany
        if(!is_null($this->withBelongsToMany) && !is_null($relationName)){
            $model->{$relationName}()->sync($this->withBelongsToMany);
        }
        return $model;
    }
    
    public function udpate(array $data){
        return $this->model->update($data);
    }
    
    public function delete($id, $hardDelete = false){
        $model = $this->model->find($id);
        if($hardDelete){
            return $model->forceDelete($id);
        }
        return $model->delete($id);
    }
    
    public function withHasMany(array $data, $relatedModel){
        $this->withHasMany = [];
        foreach($data as $k=>$v){
            $this->withHasMany[] = new $relatedModel($v);
        }
        return $this;
    }
    
    public function withBelongsToMany(array $data){
        $this->withBelongsToMany = $data;
        return $this;
    }
    
}