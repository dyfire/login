<?php

use Encore\Login\Http\Controllers\LoginController;

Route::get('auth/login', LoginController::class . '@login');
Route::post('auth/login', LoginController::class . '@postLogin');
