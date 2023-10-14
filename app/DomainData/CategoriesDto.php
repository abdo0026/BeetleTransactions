<?php

namespace App\DomainData;

trait CategoriesDto {

    public function getRules(array $fields = []): array {
        $data = [
            'name' => 'required|string',
        ];

       if(sizeof($fields) == 0) return $data;
       return array_intersect_key($data, array_flip($fields));
    }


    public function initializeCategoriesDto(): void {
        $this->fillable = array_keys($this->getRules());
    }
}
