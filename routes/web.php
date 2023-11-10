<?php

use App\Http\Controllers\WebAuthnController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return View::make('pages.welcome');
});

Route::get('/getargs', [WebAuthnController::class, 'getArgs']);

Route::post('/processCreate', [WebAuthnController::class, 'processCreate']);
//Route::get('/processCreate', [WebAuthnController::class, 'processCreate']);

Route::post('/test419', [WebAuthnController::class, 'test419']);
//Route::get('/test419', [WebAuthnController::class, 'test419']);

