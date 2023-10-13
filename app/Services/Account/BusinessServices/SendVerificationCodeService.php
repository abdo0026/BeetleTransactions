<?php
namespace App\Services\Account\BusinessServices;

use App\Services\IBusinessService;
use App\Repositories\Repository;
use App\Services\Account\CrudServices\UserCrudService;
use App\Services\Account\CrudServices\RegisterationValidationCrudService;
use App\Traits\GenerateRandomString;
use Carbon\Carbon;
use App\Jobs\SendValidationEmail;

class SendVerificationCodeService implements IBusinessService{
    use GenerateRandomString;

    private Repository $validationRepository;
    private ?object $registerationValidation;
    private Repository $userRepository;
    private ?object $user;

    public function __construct
    (
        private RegisterationValidationCrudService $registerationValidationCrudService,
        private UserCrudService $userCrudService
    )
    {
        $this->validationRepository = Repository::getRepository('RegisterationValidation');
        $this->userRepository = Repository::getRepository('User');
        $this->registerationValidation = null;
        $this->user = null;
    }

    public function isValid(array $request, \stdClass &$output): bool
    {

        $this->user = $this->userRepository->getByKey('email' , $request['email'])->first();
        if(is_null($this->user)){
            $output->Error = ['undefined email', 'بريد إلكتروني غير معروف']; 
            return false;
        }
        
        $this->registerationValidation = $this->user->registerationValidation;
        if($this->registerationValidation->is_verified_email){
            $output->Error = ['User already verified', 'المستخدم مفعل من قبل']; 
            return false;
        }
        return true;    
    }

    public function perform(array $request, \stdClass &$output): void
    {
        if(!$this->isValid($request, $output)) return;
        
        $request['id'] = $this->registerationValidation->id;
        $request['verification_code'] = $this->generateRandomString(config('globals.email_code_size'));
        $request['expire_date'] = Carbon::now()->addHours(24);
        $this->registerationValidationCrudService->update($request, $output);
        
        SendValidationEmail::dispatch($this->user->id);
        
        $output = new \stdClass;
    }
}