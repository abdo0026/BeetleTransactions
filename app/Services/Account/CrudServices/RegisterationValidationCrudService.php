<?php
namespace App\Services\Account\CrudServices;

use App\Services\CrudService;
use App\Traits\GenerateRandomString;
use Carbon\Carbon;

class RegisterationValidationCrudService extends CrudService{
    use GenerateRandomString;
    
    public function __construct(){
        parent::__construct("RegisterationValidation");
    }

    public function create(array $request ,\stdClass &$output) :void
    {
        $request['verification_code'] = $this->generateRandomString(config('globals.email_code_size'));
        $request['expire_date'] = Carbon::now()->addHours(24);
        $entity = $this->repository->create($request);   
        $output->{$this->entityName} = $entity;
    }

    
}