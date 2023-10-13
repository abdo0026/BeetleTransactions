<?php

namespace App\Services\Account\BusinessServices;

use App\Services\IBusinessService;
use App\Repositories\Repository;
use App\Services\Account\CrudServices\UserCrudService;
use App\Services\Account\CrudServices\RegisterationValidationCrudService;
use App\Events\Account\UserRegisterred;
use App\Traits\GenerateRandomString;
use App\Models\User;


class UserRegisterService implements IBusinessService {
    use GenerateRandomString;

    private Repository $userRepository;
    private ?User $user;
    

    public function __construct(
        private UserCrudService $userCrudService,
        private RegisterationValidationCrudService $registerationValidationCrudService
    ) {
        $this->userRepository = Repository::getRepository('user');
        $this->user = null;
    }

    public function isValid(array $request, \stdClass &$output): bool {
        
        $this->user = $this->userRepository->getByKey('email', $request['email'], ['registerationValidation'])->first();

        if (is_null($this->user)) return true;

        $output->Error = ['Email already Exists', 'البريد الالكتروني موجود بالفعل'];
        return false;
    }

    public function perform(array $request, \stdClass &$output): void {
        if (!$this->isValid($request, $output)) return;

        //create user        
        $this->userCrudService->create($request, $output);
        if (isset($output->Error)) return;

        //create registeration Validation
        $request['user_id'] = $output->user->id;
        $this->registerationValidationCrudService->create($request, $output);
        
        
        
        //raise an event
        UserRegisterred::dispatch($output->user, $output->registeration_validation);

        unset($output->registeration_validation);
    }
}
