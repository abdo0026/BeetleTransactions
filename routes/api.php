<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Account\LoginRegisterController;
use App\Http\Controllers\Transactions\TransactionController;

Route::group(['middleware' => ['check_file_extentions']], function () {

    Route::post('account/register', [LoginRegisterController::class, 'InternalDispatcher']);
    Route::get('account/verify', [LoginRegisterController::class, 'InternalDispatcher']);
    Route::post('account/login', [LoginRegisterController::class, 'InternalDispatcher']);
    Route::post('account/forgetPassword', [LoginRegisterController::class, 'InternalDispatcher']);
    Route::post('account/verifyAndChangePassword', [LoginRegisterController::class, 'InternalDispatcher']);
    Route::get('account/sendVerificationCode', [LoginRegisterController::class, 'InternalDispatcher']);


    Route::group(['middleware' => ['auth', 'cache_user']], function () {
        
        Route::group(['prefix' => 'account'], function () {
            Route::post('resetPassword', [LoginRegisterController::class, 'InternalDispatcher']);
        });     


        Route::group(['prefix' => 'transaction'], function () {
            Route::post('getByFilter', [TransactionController::class, 'InternalDispatcher']);
            Route::get('getById', [TransactionController::class, 'InternalDispatcher']);
        });

        
    });
    
});
