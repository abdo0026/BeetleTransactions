<?php

namespace App\Services\Account\BusinessServices;

use App\Services\IBusinessService;
use App\Repositories\Repository;
use Carbon\Carbon;

class VerifyUserService implements IBusinessService
{

    private Repository $validationRepository;
    private ?object $registerationValidation;

    public function __construct()
    {
        $this->validationRepository = Repository::getRepository('RegisterationValidation');
        $this->registerationValidation = null;
    }

    public function isValid(array $request, \stdClass &$output) :bool
    {
        $this->registerationValidation = $this->validationRepository->getByKeyValues(['verification_code' => $request['code'], 'user_id' => $request['uid']])->first();
        if(is_null($this->registerationValidation)){
            $output->Error = ['Incorrect validation code', 'رمز التحقق غير صحيح'];
            return false;
        }elseif($this->registerationValidation->is_verified_email){
            $output->Error = ['User already verified', 'المستخدم مفعل من قبل'];
            return false;
        }elseif(Carbon::now()->toDateTimeString() > $this->registerationValidation->expire_date ){
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
        $this->validationRepository->update($this->registerationValidation , $request);
        $output->verified = true;
    }



}
