<?php

namespace App\Services\Account\BusinessServices;

use App\Services\IBusinessService;
use App\Repositories\Repository;
use App\Services\Account\CrudServices\UserCrudService;
use App\Services\Account\CrudServices\RegisterationValidationCrudService;
use App\Jobs\SendForgetPasswordMail;
use App\Traits\GenerateRandomString;
use Carbon\Carbon;

class ForgetPasswordService implements IBusinessService {

    use GenerateRandomString;

    private Repository $validationRepository;
    private ?object $registerationValidation;
    private Repository $userRepository;
    private ?object $user;
    

    public function __construct(
        private RegisterationValidationCrudService $registerationValidationCrudService,
        private UserCrudService $userCrudService
    ) {
        $this->validationRepository = Repository::getRepository('RegisterationValidation');
        $this->userRepository = Repository::getRepository('User');
        $this->registerationValidation = null;
        $this->user = null;
    }

    public function isValid(array $request, \stdClass &$output): bool {
        if (strlen($request['email']) > 0){
            
            $this->user = $this->userRepository->getByKey('email', $request['email'], ['registerationValidation'])->first();
            
            if (is_null($this->user)) {
                $output->Error = ['Invalid email', 'بريد إلكتروني خاطئ'];
                return false;
            }
            
        }
        return true;
    }

    public function perform(array $request, \stdClass &$output): void {
        if (!$this->isValid($request, $output)) return;

        $this->registerationValidation = $this->user->registerationValidation;

        if (is_null($this->registerationValidation)) {
            $dataArray['user_id'] = $this->user->id;
            $this->registerationValidationCrudService->create($dataArray, $output);
            $this->registerationValidation = $output->registeration_validation;
        } else {
            $dataArray['verification_code'] = $this->generateRandomString(config('globals.email_code_size'));
            $dataArray['expire_date'] = Carbon::now()->addHours(24);
            $dataArray['is_verified_email'] = false;
            $this->registerationValidation = $this->validationRepository->update($this->registerationValidation, $dataArray);
        }
    
        
        SendForgetPasswordMail::dispatch($this->user->id);
        
    }
}
