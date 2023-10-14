<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Enum\ROLES;
use Spatie\Permission\Models\Role;


class CreateAdminRole extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Role::firstOrCreate(['name' => ROLES::ADMIN]);
    }
}
