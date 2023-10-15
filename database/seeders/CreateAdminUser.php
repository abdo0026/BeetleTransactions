<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Services\Account\CrudServices\UserCrudService;
use App\Services\Account\CrudServices\RegisterationValidationCrudService;
use App\Enum\ROLES;


class CreateAdminUser extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $userCrudService = resolve(UserCrudService::class);
        $registerationValidationCrudService = resolve(RegisterationValidationCrudService::class);
        $output = new \stdClass;

        $userCrudService->create([
            'name' => 'Admin',
            'email' => 'admin@beetle.com',
            "password" => 'admin'
        ], $output);
        
        
        $registerationValidationCrudService->create([
            'user_id' => $output->user->id,
            'is_verified_email' => true
        ], $output);

        $output->user->removeRole(ROLES::CUSTOMER->value);
        $output->user->assignRole(ROLES::ADMIN->value);
        
    }
}
