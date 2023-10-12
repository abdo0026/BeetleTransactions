<?php

namespace App\Traits;

use App\Models\Branch;
use App\Models\Title;

trait FilterHelper
{
    public function getPageSize($page_size, $count) :int
    {
        if($count < $page_size){
            return ($page_size*2) - $count;
        }
        return $page_size;
    }

    public function getDistinctObjectsFromArrayByProperty($array, $property)
    {
        $properties = array();
        $results = array();
        foreach ($array as $obj){
            if(!in_array($obj[$property], $properties)){
                array_push($properties, $obj[$property]);
                array_push($results, $obj);
            }

        }
        return $results;
    }

    public function getRelatedObjectsDistinct(array $request, \stdClass &$output) :void
    {

        if(isset($request['related_objects'])){

            foreach ($request['related_objects'] as $objName){
                $result = $this->getDistinctObjectsFromArrayByProperty($output->{$this->entityName}->{$objName}->toArray(), 'id');

                unset($output->{$this->entityName}->{$objName});
                $output->{$this->entityName}->{$objName} = $result;
            }
        }
    }

    public function getByFilterRelatedObjectsDistinct(array $request, \stdClass &$output) :void
    {

        if(isset($request['related_objects'])){
            $output->{$this->entityName}->getCollection()->transform(function ($value) use ($request) {
                foreach ($request['related_objects'] as $objName){
                        $data = $this->getDistinctObjectsFromArrayByProperty($value->$objName, 'id');
                        unset($value->$objName);
                        $value->$objName = $data;

                }
                return $value;
            });
        }



    }

}
