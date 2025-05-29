<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\QueryController;
use App\Http\Middleware\MySQLAuthMiddleware;
use Illuminate\Support\Facades\Route;

Route::get('/login', [AuthController::class, 'viewLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'viewRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware([MySQLAuthMiddleware::class])->group(function () {
    Route::get('/', function () {
        return view('console');
    });
    Route::post('/ejecutar', [QueryController::class, 'ejecutar'])->name('ejecutar');
    Route::post('/seleccionar-bd', [AuthController::class, 'seleccionarBD'])->name('seleccionar-bd');
});
