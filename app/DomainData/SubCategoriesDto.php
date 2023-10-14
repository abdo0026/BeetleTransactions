<?php

namespace App\DomainData;

trait SubCategoriesDto {

    public function getRules(array $fields = []): array {
        $data = [
            'category_id' => 'required|numeric',
            'name' => 'required|string',
        ];

       if(sizeof($fields) == 0) return $data;
       return array_intersect_key($data, array_flip($fields));
    }


    public function initializeSubCategoriesDto(): void {
        $this->fillable = array_keys($this->getRules());
    }
}
