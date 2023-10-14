<?php

namespace App\Services\Categories\CrudServices;

use App\Services\CrudService;
use App\Repositories\Repository;

class SubCategoriesCrudService extends CrudService{
    public function __construct(){
         parent::__construct("SubCategories");


    }

    public function isValidCreate(array $request, \stdClass &$output): bool
    {
        if(!parent::isValidCreate($request, $output)) return false;
    
        //category must exist
        if(isset($request['category_id'])){
            $categoryRepository = Repository::getRepository("Categories");
            $categoryNotExists = is_null($categoryRepository->getById($request['category_id']));
            if($categoryNotExists){
               $output->Error = ['Invalid Category Id', 'المعرف الخاص الفئات غير صحيح']; 
               return false;
            }
        }

       //name is unique within a category
       if(isset($request['name'])){
           
           $nameExists = $this->repository->getByKeyValues(['category_id' => $request['category_id'],  'name' => $request['name']])->exists();
           if($nameExists){
             $output->Error = ['Sub category already exists', 'الفئات موجودة بالفعل']; 
             return false;
           }

       }

       return true;
    }

    public function isValidUpdate(array $request, \stdClass &$output): bool
    {
        if(!parent::isValidUpdate($request, $output)) return false;
       
        //name is unique within a category
        if(isset($request['name'])){
           if(strcmp($this->entity->name, $request['name']) !== 0){
            $nameExists = $this->repository->getByKeyValues(['category_id' => $this->entity->category_id,  'name' => $request['name']])->exists();
               if($nameExists){
                $output->Error = ['Sub category already exists', 'الفئات موجودة بالفعل']; 
                   return false;
                }
            }
        }
        
        return true;
    }
    
}