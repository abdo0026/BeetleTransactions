<?php

namespace App\Services\Role\BusinessServices;

use App\Services\IBusinessService;
use App\Repositories\Repository;
use App\Enum\ROLES;

class AssignAdminRoleToUserService implements IBusinessService {

    private Repository $userRepository;
    private ?object $user;
    

    public function __construct() {
        $this->userRepository = Repository::getRepository('User');
        $this->user = null;
    }

    public function isValid(array $request, \stdClass &$output): bool {
    
        
        $this->user = $this->userRepository->getByKey('email', $request['email'], ['registerationValidation'])->first();
        
        if (is_null($this->user)) {
            $output->Error = ['Invalid email', 'بريد إلكتروني خاطئ'];
            return false;
        }
        
    
        return true;
    }

    public function perform(array $request, \stdClass &$output): void {
        if (!$this->isValid($request, $output)) return;
        
        $this->user->assignRole(ROLES::ADMIN->value);
    }
}
