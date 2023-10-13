<?php
namespace App\DomainData;

trait FilterDto{

    private array $filter = [
        'page' => ['required' , 'min:1' , 'integer'],
        'filters' => '',
        'related_objects.*' => '',
        'related_objects_count.*' => '',
        'page_size' => ['required' , 'min:1' , 'integer']
    ];


    public function getFilterRules(array $fields = [],string $prefix = null) :array
    {
        if(sizeof($fields) == 0)
            return $this->filter;
        return array_intersect_key($this->filter, array_flip($fields));
    }
}
