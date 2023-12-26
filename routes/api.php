<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LoginController;
use App\Http\Controllers\TaskController;
use Spatie\Permission\Models\Role;

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

Route::post('/login', [LoginController::class,'login'])->name('login');

//admin different routes        
Route::middleware(['auth:api','role:admin'])->group(function () {
    Route::post('/user-save', [LoginController::class,'register']);
    Route::post('/delete-user', [LoginController::class,'deleteUser']);
    Route::post('/view-employees', [LoginController::class,'List']);
    Route::post('/view-report', [TaskController::class,'Report']);
});

// dept_head different routes
Route::middleware(['auth:api','role:dept_head'])->group(function () {
    Route::post('/create-task', [TaskController::class,'store']);
    Route::post('/delete-task', [TaskController::class,'deleteUser']);
    Route::post('/allocate-task', [TaskController::class,'allocate']);
});

// dept_head and employee comman routes
Route::middleware(['auth:api','role:dept_head|employee'])->group(function () {
    Route::post('/view-tasks', [TaskController::class,'List']);
    Route::post('/submit-report', [TaskController::class,'emp_report']);
});
