<?php

use App\Http\Controllers\APIController;
use App\Http\Controllers\JobController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

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


Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/token', [AuthController::class, 'token']);

// Users
Route::group(["prefix" => "user"], function () {
    Route::get('/', [UserController::class, 'user']);
    Route::get('/count', [UserController::class, 'getCount']);
    Route::post('/make-admin', [UserController::class, 'makeAdmin']);
    Route::post('/image', [UserController::class, 'uploadImage']);
    Route::post('/favorite', [UserController::class, 'favorite']);
    Route::post('/delete', [UserController::class, 'delete']);
    Route::post('/restore', [UserController::class, 'restore']);
    Route::get('/all', [UserController::class, 'getUsers']);
});

//Jobs
Route::group(["prefix" => "jobs"], function () {
    Route::get('/all', [JobController::class, 'getJobs']);
    Route::get('/count', [JobController::class, 'getCount']);
    Route::get('/tags', [JobController::class, 'getTags']);
    Route::post('/create', [JobController::class, 'createJob']);
    Route::post('/activate', [JobController::class, 'activateJob']);
    Route::post('/delete', [JobController::class, 'deleteJob']);
    Route::post('/restore', [JobController::class, 'restoreJob']);
});

//Languages
Route::group(["prefix" => "languages"], function () {
    Route::get('/all', [LanguageController::class, 'getLanguages']);
});

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/
