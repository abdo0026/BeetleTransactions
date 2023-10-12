<?php

namespace App\DomainData;

trait UserDto {

    public function getRules(array $fields = [], string $prefix = null): array {
        $data = [
            'name' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string',
            'salt' => 'required|string',
        ];

       if(sizeof($fields) == 0) return $data;
       return array_intersect_key($data, array_flip($fields));
    }


    public function initializeUserDto(): void {
        $this->fillable = array_keys($this->getRules());
    }
}
