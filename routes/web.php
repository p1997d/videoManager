<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Main;
use App\Http\Controllers\Main\IndexController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


Route::group(['namespace' => Main::class], function () {
    Route::get('/', [IndexController::class, 'index'])->name('main.index');
    Route::post('/fileUpload', [IndexController::class, 'fileUpload'])->name('main.file.upload');
    Route::get('/{video}/fileRemove', [IndexController::class, 'fileRemove'])->name('main.file.remove');
});
