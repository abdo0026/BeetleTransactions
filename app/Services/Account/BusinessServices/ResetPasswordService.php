<?php
namespace App\Services\Account\BusinessServices;

use App\Repositories\Repository;
use App\Services\IBusinessService;
use App\Services\Account\CrudServices\UserCrudService;
use Illuminate\Support\Facades\Hash;

class ResetPasswordService implements IBusinessService{
    
    private Repository $userRepository;
    private ?object $user;

    public function __construct(
        private UserCrudService $userCrudService
    )
    {
        $this->userRepository = Repository::getRepository('User');
        $this->user = null;
    }

    public function isValid(array $request, \stdClass &$output): bool
    {        
        $this->user = config('globals.user');
        
        if(is_null($this->user)){
            $output->Error = ['Somthing went wrong, pleas login again', 'حدث خطأ ما ، يرجى تسجيل الدخول مرة أخرى']; 
            return false;
        }elseif(isset($request['old_password']) && !Hash::check($request['old_password'] . $this->user->salt, $this->user->password)){
            $output->Error = ['Old password is not correct', 'كلمة المرور القديمة غير صحيحة']; 
            return false;
        }elseif(Hash::check($request['password'] . $this->user->salt, $this->user->password)){
            $output->Error = ['password did not change, please select new password',
                              'لم تتغير كلمة المرور ، يرجى تحديد كلمة مرور جديدة']; 
            return false;
        }

        return true;    
    }

    public function perform(array $request, \stdClass &$output): void
    {
        if(!$this->isValid($request, $output)) return;
        $request['id'] = $this->user->id;
        $this->userCrudService->update($request, $output);

        $userTokens = $this->user->tokens;
        foreach($userTokens as $token) {
            $token->revoke();   
        }
    }

}
