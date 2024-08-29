<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login']);
Route::get('register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [AuthController::class, 'register']);

Route::middleware('auth')->group(function () {
    Route::middleware(['role:admin'])
    ->group(function () {
        Route::resource('products', ProductController::class);
    });

    Route::middleware(['role:user'])
    ->group(function () {
        Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    });

    Route::post('logout', [AuthController::class, 'logout'])->name('logout');
});

Route::get('/', DashboardController::class);