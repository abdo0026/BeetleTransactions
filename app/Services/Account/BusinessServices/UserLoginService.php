<?php
namespace App\Services\Account\BusinessServices;

use App\Services\IBusinessService;
use App\Repositories\Repository;
use Illuminate\Support\Facades\Hash;


class UserLoginService implements IBusinessService{

    private Repository $userRepository;
    private ?object $user;
    public function __construct()
    {
        $this->userRepository = Repository::getRepository('user');
        $this->user = null;
    }
    
    public function isValid(array $request ,\stdClass &$output) :bool
    {
        $this->user = $this->userRepository->getByKey('email' , $request['email'], ['registerationValidation'])->withoutGlobalScopes()->first();
        if(is_null($this->user) || !Hash::check($request['password'] . $this->user->salt, $this->user->password)) {
            $output->Error = ['incorrect username or password', 'اسم المستخدم أو كلمة المرور غير صحيحة']; 
            return false;
        }elseif(!$this->user->registerationValidation->is_verified_email){
            $output->Error = ['Account unverified', 'الحساب غير مفعل']; 
            return false;
        }

        return true;
    }

    public function perform(array $request ,\stdClass &$output) :void
    {
        if(!$this->isValid($request, $output)) return;
        $token = $this->user->createToken('Grant Client token')->accessToken;
        //auth()->login($this->user);
        $output->user = $this->user;
        $output->token = $token;
        $output->user->setRelations([]);
    }
}