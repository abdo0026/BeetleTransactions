<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Enum\ROLES;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Account\RoleHandleController;

Route::group(['middleware' => ['check_file_extentions']], function () {

    Route::group(['middleware' => ['auth', 'role:' . ROLES::ADMIN->value]], function () {

        Route::post('roles/assignAdminRole', [RoleHandleController::class, 'InternalDispatcher']);

        Route::get('test', function (){
            return response()->json(['a' => 'a'] , Response::HTTP_OK);
        });

    });
    
});