<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\IndexController;
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

Route::get('/', [IndexController::class,'home']);
Route::post('/checkout', [IndexController::class,'checkout'])->name('checkout');
