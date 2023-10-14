<?php

namespace App\Http\Controllers\Categories;

use App\Http\Controllers\Controller;
use App\DomainData\SubCategoriesDto;
use App\DomainData\FilterDto;
use App\Services\Categories\CrudServices\SubCategoriesCrudService;


class SubCategoriesController extends Controller
{
    use FilterDto, SubCategoriesDto;

    public function __construct(
        private SubCategoriesCrudService $service
    )
    {}


    public function createMany(array $request, \stdClass &$output) :void
    {
        $validator = \Validator::make($request, [
            'entity_array' => 'required|array',
        ]);

        if ($validator->fails()) {
            $output->Error = $validator->messages();
            return;
        }
        $request = $validator->validate();

        
        $rules = $this->getRules(['category_id', 'name']); 

        foreach($request['entity_array'] as $key => $entity){
            $validator = \Validator::make($entity, $rules);
            if ($validator->fails()) {
                $output->Error = $validator->messages();
                return;
            }
        }
        
        $this->service->createMany($request, $output);

    }

    public function create(array $request, \stdClass &$output) :void
    {
        $rules = $this->getRules(['category_id', 'name']);  

        $validator = \Validator::make($request, $rules);
        if ($validator->fails()) {
            $output->Error = $validator->messages();
            return;
        }

        $request = $validator->validate();
        
        $this->service->create($request, $output);

    }


    public function update(array $request, \stdClass &$output) :void
    {
        $rules = $this->getRules(['name']); 
        $rules['id'] = 'required|numeric';

        $validator = \Validator::make($request, $rules);
        if ($validator->fails()) {
            $output->Error = $validator->messages();
            return;
        }

        $request = $validator->validate();

        $this->service->update($request, $output);
    }

    public function getByFilter(array $request, \stdClass &$output) :void
    {
        $rules = $this->getFilterRules(['page', 'filters', 'related_objects.*', 'related_objects_count.*',  'page_size']);
        
        $validator = \Validator::make($request, $rules);
        if ($validator->fails()){
            $output->Error = $validator->messages();
            return;
        }

        $request = $validator->validate();

        if(!isset($request['related_objects']))
            $request['related_objects'] = [];
        if(!isset($request['related_objects_count']))
            $request['related_objects_count'] = [];

        $this->service->getByFilter($request, $output);
    }

    public function getById(array $request, \stdClass &$output) :void
    {
        $rules = $this->getFilterRules(['related_objects.*', 'related_objects_count.*']);    
        $rules['id'] = 'required|numeric'; 
        $validator = \Validator::make($request, $rules);
        if ($validator->fails()){
            $output->Error = $validator->messages();
            return;
        }

        $request = $validator->validate();
      
        
        if(!isset($request['related_objects']))
            $request['related_objects'] = [];
        if(!isset($request['related_objects_count']))
            $request['related_objects_count'] = [];

        $this->service->getById($request, $output);
    }

    public function delete(array $request, \stdClass &$output) :void
    {
        $rules['ids']  = ['required', 'array'];
        $rules['ids.*'] = ['required', 'numeric'];
        $validator = \Validator::make($request, $rules);
        if ($validator->fails()) {
            $output->Error = $this->failMessages($validator->messages());
            return ;
        }

        $request = $validator->validate();

        $this->service->delete($request , $output);
    }

}
