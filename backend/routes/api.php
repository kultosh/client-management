<?php

use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'clients'], function () {
    Route::get('/', [ClientController::class, 'index']);
});