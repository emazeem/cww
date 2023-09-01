<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\IndexController;


Route::post('login', [IndexController::class, 'login']);
Route::post('register', [IndexController::class, 'register']);
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::post('createCarSubscription', [IndexController::class, 'createCarSubscription']);
    Route::post('fetchCustomers', [IndexController::class, 'fetchCustomers']);
    Route::post('fetchSubscriptions', [IndexController::class, 'fetchSubscriptions']);
    Route::post('editUser', [IndexController::class, 'editUser']);
    Route::post('updatePassword', [IndexController::class, 'updatePassword']);
    Route::post('fetchCustomer', [IndexController::class, 'fetchCustomer']);
    Route::post('taskMarkAsDone', [IndexController::class, 'taskMarkAsDone']);
    Route::post('paymentMarkAsDone', [IndexController::class, 'paymentMarkAsDone']);
    Route::post('fetchTasks', [IndexController::class, 'fetchTasks']);
    Route::post('fetchTask', [IndexController::class, 'fetchTask']);
    Route::post('fetchCars', [IndexController::class, 'fetchCars']);
    Route::post('cancelSubscription', [IndexController::class, 'cancelSubscription']);
    Route::post('fetchUser', [IndexController::class, 'fetchUser']);
    Route::post('fetchInvoices', [IndexController::class, 'fetchInvoices']);
    Route::post('fetchActivities', [IndexController::class, 'fetchActivities']);
    Route::post('changePassword', [IndexController::class, 'changePassword']);
});
