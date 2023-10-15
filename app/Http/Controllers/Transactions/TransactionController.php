<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use App\DomainData\TransactionDto;
use App\DomainData\FilterDto;
use App\Services\Transactions\CrudServices\TransactionCrudService;


class TransactionController extends Controller
{
    use FilterDto, TransactionDto;

    public function __construct(
        private TransactionCrudService $service
    )
    {}

    public function create(array $request, \stdClass &$output) :void
    {
        $rules = $this->getRules(['user_id', 'category_id', 'sub_category_id', 'amount', 'due_date', 'vat_percentage', 'is_vat_included']);

        $validator = \Validator::make($request, $rules);
        if ($validator->fails()) {
            $output->Error = $validator->messages();
            return;
        }

        $request = $validator->validate();
        
        $this->service->create($request, $output);

    }

    public function getByFilter(array $request, \stdClass &$output) :void
    {
        $rules = $this->getFilterRules(['page', 'filters', 'related_objects.*', 'related_objects_count.*',  'page_size']);
        
        /*
        if(is_null(config('globals.user')))
        {
            $request['filters'] = [
                "transactions" => [
                    "admin_id" => auth()->user()->id,
                    "operator" => "and"
                ]
            ];
        }
        */
        
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

}
