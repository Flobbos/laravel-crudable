<?php

namespace Flobbos\Crudable;

trait Crudable {
    
    protected $relation = [];
    protected $withHasMany,$withBelongsToMany,$orderBy,$orderDirection;
    /**
     * Get a single item or collection
     * @param int $id
     * @return Model/Collection
     */
    public function get($id = null){
        if(!is_null($id)){
            return $this->find($id);
        }
        if(!is_null($this->orderBy)){
            return $this->model->with($this->relation)->orderBy($this->orderBy,$this->orderDirection)->get();
        }
        return $this->model->with($this->relation)->get();
    }
    
    /**
     * Get paginated collection
     * @param int $perPage
     * @return Collection
     */
    public function paginate($perPage){
        if(!is_null($this->orderBy)){
            return $this->model->with($this->relation)->orderBy($this->orderBy)->paginate($perPage);
        }
        return $this->model->with($this->relation)->paginate($perPage);
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
    
    /**
     * Order the collection you pull
     * @param string $field
     * @param string $order default asc
     */
    public function orderBy($field, $order = 'asc'){
        $this->orderBy = $field;
        $this->orderDirection = $order;
        return $this;
    }
    
    /**
     * Create new database entry including related models
     * @param array $data
     * @param string $relationName
     * @return Model
     */
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
    
    /**
     * Update Model
     * @param array $data
     * @return bool
     */
    public function update($id, array $data, $return_model = false){
        $model = $this->find($id);
        if($return_model){
            $model->update($data);
            return $model;
        }
        return $model->update($data);
    }
    
    /**
     * Delete model either soft or hard delete
     * @param int $id
     * @param bool $hardDelete
     * @return bool
     */
    public function delete($id, $hardDelete = false){
        $model = $this->model->find($id);
        if($hardDelete){
            return $model->forceDelete($id);
        }
        return $model->delete($id);
    }
    
    /**
     * Set related models that need to be created
     * for a hasMany relationship
     * @param array $data
     * @param string $relatedModel
     * @return self
     */
    public function withHasMany(array $data, $relatedModel){
        $this->withHasMany = [];
        foreach($data as $k=>$v){
            $this->withHasMany[] = new $relatedModel($v);
        }
        return $this;
    }
    
    /**
     * Set related models for belongsToMany relationship
     * @param array $data
     * @return self
     */
    public function withBelongsToMany(array $data){
        $this->withBelongsToMany = $data;
        return $this;
    }
    
    /**
     * Handle a file upload
     * @param \Illuminate\Http\Request $request
     * @param type $fieldname
     * @param type $folder
     * @param type $storage_disk
     * @return string filename
     */
    public function handleUpload(\Illuminate\Http\Request $request, $fieldname = 'photo', $folder = 'images', $storage_disk = 'public'){
        if(!$requet->file($fieldname)->isValid()){
            throw new \Exception(trans('crud.invalid_file_upload'));
        }
        //Get filename
        $basename = basename($request->file($fieldname)->getClientOriginalName(),'.'.$request->file($fieldname)->getClientOriginalExtension());
        $filename = str_slug($basename).'.'.$request->file($fieldname)->getClientOriginalExtension();
        //Move file to location
        $request->file($fieldname)->storeAs($folder,$filename,$storage_disk);
        return $filename;
    }
    
}