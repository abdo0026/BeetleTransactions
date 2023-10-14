<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\DomainData\UserDto;
use App\Services\Role\BusinessServices\AssignAdminRoleToUserService;

class RoleHandleController extends Controller
{
    use UserDto;

    public function __construct(
        private AssignAdminRoleToUserService $assignAdminRoleToUserService
    )
    {}

    public function assignAdminRole(array $request, \stdClass &$output): void {
        $rules = $this->getRules(['email']);

        $validator = \Validator::make($request, $rules);
        if ($validator->fails()) {
            $output->Error = $validator->messages();
            return;
        }
        $request = $validator->validate();

        $this->assignAdminRoleToUserService->perform($request, $output);
    } 
}
