<?php 

namespace App\Http\Repositories;

use Illuminate\Database\Eloquent\Model;

class ModelRepository
{
    // model property on class instances
    protected $model;
    protected $relations;
    // Constructor to bind model to repo
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    // Get all instances of model
    public function all()
    {
        return $this->model->all();
    }

    // Get paginated records of model
    public function getPaginatedRecord($request=null, $conditions=[])
    {
        $record = $this->model::query();

        if ($conditions) {
            $record = $this->model->query()->where($conditions);
        }

        if($this->relations) {
            $record->with($this->relations);
        }

        if ($request['sort_by'] != 'null' && $request['sort_desc']) {
            $record->orderBy($request['sort_by'], ($request['sort_desc'] === "true"?"desc":"asc"));
        } else {
            $record->orderBy($this->model->getKeyName(), "desc");
        }
            
        return $record->paginate($request['items_per_page']);
    }

    // create a new record in the database
    public function create(array $data)
    {
        return $this->model->create($data);
    }

    // update record in the database
    public function update(array $data, Object $old_data)
    {
        return $old_data->update($data);
    }

    // remove record from the database
    public function delete($id)
    {
        return $this->model->destroy($id);
    }

    // show the record with the given id
    public function show($id)
    {
        return $this->relations
            ? $this->model->with($this->relations)->findOrFail($id)
            : $this->model->findOrFail($id);
    }

    // Get the associated model
    public function getModel()
    {
        return $this->model;
    }

    // Set the associated model
    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    // Eager load database relationships
    public function with($relations)
    {
        $this->relations = $relations;
        return $this;
    }

    // Get all list of active record
    public function getListActiveRecords(){
        return $this->model::select('id as value','name as text')->where('enabled', 1)->get();
    }
}