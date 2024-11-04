<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\TodoController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('api/v1')->group(function () {
    // User routes
    Route::get('/users', [UserController::class, 'usersIndex']);
    Route::post('/users', [UserController::class, 'usersCreate']);
    Route::get('/users/{userId}', [UserController::class, 'userRead']);
    Route::patch('/users/{userId}', [UserController::class, 'userUpdate']);
    Route::delete('/users/{userId}', [UserController::class, 'userDelete']);
    Route::get('/users/{userId}/todos', [UserController::class, 'userGetTodos']);

    // Todo routes
    Route::get('/todos', [TodoController::class, 'todosIndex']);
    Route::post('/todos', [TodoController::class, 'todosCreate']);
    Route::get('/todos/{todoId}', [TodoController::class, 'todoRead']);
    Route::patch('/todos/{todoId}', [TodoController::class, 'todoUpdate']);
    Route::delete('/todos/{todoId}', [TodoController::class, 'todoDelete']);
});
