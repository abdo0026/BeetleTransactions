<?php
namespace App\Services\Account\BusinessServices;

use App\Services\IBusinessService;
use App\Repositories\Repository;
use App\Services\Account\CrudServices\UserCrudService;
use Carbon\Carbon;


class VerifyAndChangePasswordService implements IBusinessService{
   

    private Repository $validationRepository;
    private Repository $userRepository;
    private ?object $registerationValidation;
    private ?object $user;
    
    public function __construct(
        private UserCrudService $userCrudService
    )
    {
        $this->validationRepository = Repository::getRepository('RegisterationValidation');
        $this->userRepository = Repository::getRepository('User');
        $this->registerationValidation = null;
        $this->user = null;
    }

    public function isValid(array $request, \stdClass &$output) :bool
    {
        $this->user = $this->userRepository->getById($request['uid'], ['registerationValidation']);
        
        if(is_null($this->user) || $this->user->registerationValidation->verification_code !== $request['code']){
            $output->Error = ['Incorrect validation code', 'رمز التحقق غير صحيح']; 
            return false;
        }
        
        $this->registerationValidation = $this->user->registerationValidation;
       if($this->registerationValidation->is_verified_email){
            $output->Error = ['User already changed password', 'قام المستخدم بالفعل بتغيير كلمة المرور']; 
            return false;
        }
        if(Carbon::now()->toDateTimeString() > $this->registerationValidation->expire_date ){
            $output->Error = ['Verification code expired', 'صلاحية الرابط انتهت']; 
            return false;
        }
       

        return true;
    }


    public function perform(array $request, \stdClass &$output): void
    {
        if(!$this->isValid($request, $output)) return;

        $request['is_verified_email'] = true;
        $request['account_verified_at'] = Carbon::now()->toDateTimeString();
        $request['verification_code'] = '';
        $this->validationRepository->update($this->registerationValidation , $request);
        

        $this->userCrudService->update([
            'id' => $request['uid'],
            'password' => $request['password']
        ], $output);
    }
}