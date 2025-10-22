<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\ApplicationController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/register',[RegisterController::class,"store"]);
Route::post('/login',[LoginController::class,"login"]);
Route::middleware('auth:sanctum')->post('/logout',[LoginController::class,"logout"]);



//pulic route 

Route::get('/jobs', [JobController::class, 'index']);
Route::get('/jobs/{id}', [JobController::class, 'show']);
Route::post('/jobs/search', [JobController::class, 'search']);

// admin routes 

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/users', [AdminController::class, 'getAllUsers']);
    Route::get('/personal-data', [AdminController::class, 'getPersonalDataFromAdmin']);
    Route::put('/users/{id}', [AdminController::class, 'updateUserData']);
    Route::delete('/users/{id}', [AdminController::class, 'deleteUser']);
    Route::get('/applications', [ApplicationController::class, 'getAllApplications']);
});



// Job Controller

Route::middleware('auth:sanctum','role:employeur|admin')->group(function(){
    Route::post('/jobs',[JobController::class,'store']);
    Route::put('/jobs/{id}',[JobController::class,'update']);
    Route::delete('/jobs/{id}',[JobController::class,'destroy']);
});


// employer controller 

Route::middleware(['auth:sanctum', 'role:employeur'])->group(function () {
    Route::get('/employer/applications', [ApplicationController::class, 'getEmployerApplications']);
});

// user controller

Route::middleware('auth:sanctum','role:user')->group(function(){
    Route::get('/personal-data',[UserController::class,'getPersonalDataFromUser']);
    Route::post('/applications',[UserController::class,'storeApplication']);
    Route::get('/applications',[UserController::class,'getOwnApplications']);

});