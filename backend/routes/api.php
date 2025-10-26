<?php

use App\Http\Controllers\ClientController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'clients'], function () {
    Route::get('/', [ClientController::class, 'index']);
    Route::post('/import', [ClientController::class, 'import']);
    Route::get('imports/{importId}/status', [ClientController::class, 'getImportStatus'])->name('clients.import.status');;
});