<?php

namespace App\Services\Categories\BusinessServices;

use App\Services\IBusinessService;
use App\Repositories\Repository;
use App\Services\Categories\CrudServices\CategoriesCrudService;
use App\Services\Categories\CrudServices\SubCategoriesCrudService;

class CreateCategoryWithSubCategoriesService implements IBusinessService {

    public function __construct(
        private CategoriesCrudService $categoriesCrudService,
        private SubCategoriesCrudService $subCategoriesCrudService
    ) {}

    public function isValid(array $request, \stdClass &$output): bool {
    
        return true;
    }

    public function perform(array $request, \stdClass &$output): void {
        if (!$this->isValid($request, $output)) return;

        $this->categoriesCrudService->create($request, $output);
        if(isset($output->Error)) return;

        $subCategoriesNames = $request['sub_categories'];
        $categoryId = $output->categories->id;

        $subCategoriesArray['entity_array'] = array_map( function ($name) use ($categoryId){
            return [
               "category_id" => $categoryId,
               "name" =>$name
             ];
         } ,$subCategoriesNames);
         
         $subCategoriesOutput = new \stdClass;
         $this->subCategoriesCrudService->createMany($subCategoriesArray, $subCategoriesOutput);
    }
}
