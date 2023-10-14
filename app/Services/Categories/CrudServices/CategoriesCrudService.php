<?php

namespace App\Services\Categories\CrudServices;

use App\Services\CrudService;


class CategoriesCrudService extends CrudService{
    public function __construct(){
         parent::__construct("Categories");
    }

    public function isValidCreate(array $request, \stdClass &$output): bool
    {
       if(!parent::isValidCreate($request, $output)) return false;
       
       //name is unique
       if(isset($request['name'])){
           $nameExists = $this->repository->getByKey('name', $request['name'])->exists();
           if($nameExists){
             $output->Error = ['Category already exists', 'الفئات موجودة بالفعل']; 
             return false;
           }
       }

       return true;
    }

    public function isValidUpdate(array $request, \stdClass &$output): bool
    {
       if(!parent::isValidUpdate($request, $output)) return false;

       //name is unique
        if(isset($request['name'])){
           if(strcmp($this->entity->name, $request['name']) !== 0){
               $nameExists = $this->repository->getByKey('name', $request['name'])->exists();
               if($nameExists){
                $output->Error = ['Category already exists', 'الفئات موجودة بالفعل']; 
                   return false;
                }
            }
        }
        return true;
    }
    
}