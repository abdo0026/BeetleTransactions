<?php
 namespace App\Services;

 use App\Repositories\Repository;

class CrudService implements ICrudService{
    protected Repository $repository;
    protected string $entityName;

    protected ?object $entity;

    public function __construct(string $entityName)
    {
        $this->entityName = strtolower(preg_replace('/\B([A-Z])/', '_$1', $entityName));
        $this->repository  = Repository::getRepository($entityName);
        $this->entity = null;
    }

    public function isValidCreate(array $request, \stdClass &$output): bool
    {
        return true;
    }

    public function createMany(array $request ,\stdClass &$output) :void
    {

        $related_objects = $request['related_objects'] ?? [];
        $output->entity_array = [];
        foreach($request['entity_array'] as $entity){
            $entity['related_objects'] = $related_objects;
            $this->create($entity, $output);
            if(isset($output->Error)) return;
            $output->entity_array[] = $output->{$this->entityName};
        }


        unset($output->{$this->entityName});
        
    }

    public function updateMany(array $request ,\stdClass &$output) :void
    {
        $related_objects = $request['related_objects'] ?? [];
        $output->entity_array = [];
        foreach($request['entity_array'] as $entity){
            $entity['related_objects'] = $related_objects;
            $this->update($entity, $output);
            if(isset($output->Error)) return;
            $output->entity_array[] = $output->{$this->entityName};
        }


        unset($output->{$this->entityName});
    }

    public function create(array $request ,\stdClass &$output) :void
    {
        if(!$this->isValidCreate($request, $output)) return;
        $related_objects = $request['related_objects'] ?? [];
        $this->entity = $this->repository->create($request, $related_objects);   
        $output->{$this->entityName} = $this->entity;//
    }

    public function isValidUpdate(array $request, \stdClass &$output): bool
    {
        $this->entity = $this->repository->getById($request['id']);
        if(is_null($this->entity))
        {
            $output->Error = ['Wrong identifier', 'معرف خاطئ'];     
            return false;
        }

        return true;
    }

    public function update(array $request ,\stdClass &$output) :void
    {
        if(!$this->isValidUpdate($request, $output)) return;
        $related_objects = $request['related_objects'] ?? [];
        $output->{$this->entityName} = $this->repository->update($this->entity , $request, $related_objects);
    }

    public function delete(array $request ,\stdClass &$output) :void
    {
        $output->{$this->entityName} = $this->repository->delete($request['ids']);
    }

    public function getByFilter(array $request ,\stdClass &$output) :void
    {
        $output->{$this->entityName} =  $this->repository->getByFilter($request['filters'] , $request['related_objects'] , $request['related_objects_count'])->paginate($request['page_size']);
    }

    public function getById(array $request ,\stdClass &$output) :void
    {
        $output->{$this->entityName} = $this->repository->getById($request['id'] , $request['related_objects'] , $request['related_objects_count']);
    }
}

