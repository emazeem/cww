<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\IndexController;
use App\Http\Controllers\WafeqInvoiceController;
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
Route::get('invoice/create', [WafeqInvoiceController::class,'create'])->name('invoice.create');
Route::get('invoice/download', [WafeqInvoiceController::class,'download'])->name('invoice.download');


