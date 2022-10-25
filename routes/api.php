<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserRoleController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::apiResource('/users', UserController::class)
     ->except(['edit', 'create', 'store', 'update'])
     ->middleware(['auth:sanctum', 'ability:admin,super-admin']);

Route::post('/users', [UserController::class, 'store']);
Route::put('/users/{user}', [UserController::class, 'update'])->middleware(['auth:sanctum', 'ability:admin,super-admin,usuario']);
Route::post('/users/{user}', [UserController::class, 'update'])->middleware(['auth:sanctum', 'ability:admin,super-admin,usuario']);
Route::patch('/users/{user}', [UserController::class, 'update'])->middleware(['auth:sanctum', 'ability:admin,super-admin,usuario']);
Route::get('/me', [UserController::class, 'me'])->middleware('auth:sanctum');
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');

Route::apiResource('/roles', RoleController::class)
     ->except(['create', 'edit'])
     ->middleware(['auth:sanctum', 'ability:admin,super-admin']);

Route::apiResource('/users.roles', UserRoleController::class)
     ->except(['create', 'edit', 'show', 'update'])
     ->middleware(['auth:sanctum', 'ability:admin,super-admin']);