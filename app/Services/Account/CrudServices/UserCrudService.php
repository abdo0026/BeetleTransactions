<?php

namespace App\Services\Account\CrudServices;

use App\Services\CrudService;
use Illuminate\Support\Facades\Hash;
use App\Traits\GenerateRandomString;

class UserCrudService extends CrudService {
    use GenerateRandomString;

    public function __construct() {
        parent::__construct('User');
    }

    public function create(array $request, \stdClass &$output): void {
        
        if (isset($request['password'])) {
            $salt = $this->GenerateRandomString(30);
            $request['password'] = Hash::make($request['password'] . $salt);
            $request['salt'] = $salt;
        }

        $entity = $this->repository->create($request);
        $output->{$this->entityName} = $entity;
    }


    public function update(array $request, \stdClass &$output): void {
       if (!$this->isValidUpdate($request, $output)) return;

       if (isset($request['password'])) {
            $salt = $this->GenerateRandomString(30);
            $request['password'] = Hash::make($request['password'] . $salt);
            $request['salt'] = $salt;
        }

        $entity = $this->repository->getById($request['id']);
        $output->{$this->entityName} = $this->repository->update($entity, $request);
    }



    public function isValidCreate(array $request, \stdClass &$output): bool {
        if (!parent::isValidCreate($request, $output)) return false;

        //email is unique
        if (isset($request['email'])) {
            $emailExists = $this->repository->getByKey('email', $request['email'])->exists();
            if ($emailExists) {
                $output->Error = ['Email already exists', 'البريد الإلكتروني موجود بالفعل'];
                return false;
            }
        }

        return true;
    }


    public function isValidUpdate(array $request, \stdClass &$output): bool {
        if (!parent::isValidUpdate($request, $output)) return false;

        //email is unique
        if (isset($request['email'])) {
            if (strcmp($this->entity->email, $request['email']) !== 0) {
                $emailExists = $this->repository->getByKey('email', $request['email'])->exists();
                if ($emailExists) {
                    $output->Error = ['Email already exists', 'البريد الإلكتروني موجود بالفعل'];
                    return false;
                }
            }
        }

        return true;
    }
}
