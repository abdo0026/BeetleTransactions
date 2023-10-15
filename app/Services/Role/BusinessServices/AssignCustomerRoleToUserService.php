<?php

namespace App\Services\Role\BusinessServices;

use App\Services\IBusinessService;
use App\Repositories\Repository;
use App\Models\User;
use App\Enum\ROLES;

class AssignCustomerRoleToUserService implements IBusinessService {

    private Repository $userRepository;
    private ?object $user;
    

    public function __construct() {
        $this->userRepository = Repository::getRepository('User');
        $this->user = null;
    }

    public function isValid(array $request, \stdClass &$output): bool {
    
        if(isset($request['user']) && $request['user'] instanceof User)
        {
            $this->user = $request['user'];
            return true;
        }
        

        if(!isset($request['email']))
        {
            $output->Error = ['Email is required', 'البريد الالكتروني مطلوب'];
            return false;
        }

        $this->user = $this->userRepository->getByKey('email', $request['email'], ['registerationValidation'])->first();
        
        if (is_null($this->user)) {
            $output->Error = ['Invalid email', 'بريد إلكتروني خاطئ'];
            return false;
        }
        
    
        return true;
    }

    public function perform(array $request, \stdClass &$output): void {
        if (!$this->isValid($request, $output)) return;
        
        $this->user->assignRole(ROLES::CUSTOMER->value);
    }
}
