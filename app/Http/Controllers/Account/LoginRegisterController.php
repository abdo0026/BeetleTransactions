<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Services\Account\BusinessServices\VerifyAndChangePasswordService;
use App\Services\Account\BusinessServices\SendVerificationCodeService;
use App\Services\Account\BusinessServices\ForgetPasswordService;
use App\Services\Account\BusinessServices\ResetPasswordService;
use App\Services\Account\BusinessServices\UserRegisterService;
use App\Services\Account\BusinessServices\VerifyUserService;
use App\Services\Account\BusinessServices\UserLoginService;
use App\DomainData\UserDto;

class LoginRegisterController extends Controller
{
    use UserDto;

    public function __construct(
        private VerifyAndChangePasswordService $verifyAndChangePasswordService,
        private SendVerificationCodeService $sendVerificationCodeService,
        private ForgetPasswordService $forgetPasswordService,
        private ResetPasswordService $resetPasswordService,
        private UserRegisterService $userRegisterService,
        private VerifyUserService $verifyUserService,
        private UserLoginService $userLoginService,
        
        
        //private SendVerificationCodeService    $sendVerificationCodeService,
        //private ResetPasswordService           $resetPasswordService,
        
        
    ) {
    }

    public function register(array $request, \stdClass &$output): void {
        $rules = $this->getRules(['email', 'name', 'password']);

        $validator = \Validator::make($request, $rules);
        if ($validator->fails()) {
            $output->Error = $validator->messages();
            return;
        }
        $request = $validator->validate();

        $this->userRegisterService->perform($request, $output);
    } 


    public function login(array $request, \stdClass &$output): void {

        $validator = \Validator::make($request, [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            $output->Error = $validator->messages();
            return;
        }
        $request = $validator->validate();

        $this->userLoginService->perform($request, $output);
    }

    public function verify(array $request, \stdClass &$output): void {
        $rules['code'] = ['required', 'string'];
        $rules['uid'] = ['required', 'string'];
        $validator = \Validator::make($request, $rules);
        if ($validator->fails()) {
            $output->Error = $validator->messages();
            return;
        }
        $request = $validator->validate();

        $this->verifyUserService->perform($request, $output);
    }

    public function forgetPassword(array $request, \stdClass &$output): void {
        $rules = $this->getRules(['email']);
        
        $validator = \Validator::make($request, $rules);
        if ($validator->fails()) {
            $output->Error = $validator->messages();
            return;
        }
        $request = $validator->validate();

        $this->forgetPasswordService->perform($request, $output);
    }

    public function verifyAndChangePassword(array $request, \stdClass &$output): void {
        $rules = $this->getRules(['password']);
        $rules['code'] = ['required', 'string'];
        $rules['uid'] = ['required', 'string'];
        $validator = \Validator::make($request, $rules);
        if ($validator->fails()) {
            $output->Error = $validator->messages();
            return;
        }
        $request = $validator->validate();

        $this->verifyAndChangePasswordService->perform($request, $output);
    }

    public function sendVerificationCode(array $request, \stdClass &$output): void {
        
        $rules = $this->getRules(['email']);
        
        $validator = \Validator::make($request, $rules);
        if ($validator->fails()) {
            $output->Error = $validator->messages();
            return;
        }
        $request = $validator->validate();

        $this->sendVerificationCodeService->perform($request, $output);
    }


    public function resetPassword(array $request, \stdClass &$output): void {
        $rules = $this->getRules(['password']);
        $rules['old_password'] = 'required|string';
        
        $validator = \Validator::make($request, $rules);
        if ($validator->fails()) {
            $output->Error = $validator->messages();
            return;
        }
        $request = $validator->validate();

        $this->resetPasswordService->perform($request, $output);
    }
}
