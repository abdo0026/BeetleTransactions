<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Enum\ROLES;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Controllers\Account\RoleHandleController;
use App\Http\Controllers\Categories\CategoriesController;
use App\Http\Controllers\Categories\SubCategoriesController;

Route::group(['middleware' => ['check_file_extentions']], function () {

    Route::group(['middleware' => ['auth', 'role:' . ROLES::ADMIN->value]], function () {

        Route::post('roles/assignAdminRole', [RoleHandleController::class, 'InternalDispatcher']);

        Route::group(['prefix' => 'categories'], function () {
            Route::post('createMany', [CategoriesController::class, 'InternalDispatcher']);
            Route::post('create', [CategoriesController::class, 'InternalDispatcher']);
            Route::put('update', [CategoriesController::class, 'InternalDispatcher']);
            Route::post('getByFilter', [CategoriesController::class, 'InternalDispatcher']);
            Route::get('getById', [CategoriesController::class, 'InternalDispatcher']);
            Route::post('delete', [CategoriesController::class, 'InternalDispatcher']);
            Route::post('createWithSubCategory', [CategoriesController::class, 'InternalDispatcher']);
        });


        Route::group(['prefix' => 'subCategories'], function () {
            Route::post('createMany', [SubCategoriesController::class, 'InternalDispatcher']);
            Route::post('create', [SubCategoriesController::class, 'InternalDispatcher']);
            Route::put('update', [SubCategoriesController::class, 'InternalDispatcher']);
            Route::post('getByFilter', [SubCategoriesController::class, 'InternalDispatcher']);
            Route::get('getById', [SubCategoriesController::class, 'InternalDispatcher']);
            Route::post('delete', [SubCategoriesController::class, 'InternalDispatcher']);
        });

    });
    
});