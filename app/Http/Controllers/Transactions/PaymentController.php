<?php

namespace App\Http\Controllers\Transactions;

use App\Http\Controllers\Controller;
use App\DomainData\PaymentDto;
use App\DomainData\FilterDto;
use App\Services\Transactions\CrudServices\PaymentCrudService;

class PaymentController extends Controller
{
    use FilterDto, PaymentDto;

    public function __construct(
        private PaymentCrudService $service
    )
    {}

    public function create(array $request, \stdClass &$output) :void
    {
        $rules = $this->getRules(['transaction_id', 'user_id', 'amount', 'details']);

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
        $rules['id'] = ['required', 'numeric'];
        $validator = \Validator::make($request, $rules);
        if ($validator->fails()){
            $output->Error = $validator->messages();
            return;
        }

        $request = $validator->validate();

        $this->service->delete($request , $output);
    }
}
