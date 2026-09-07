<?php

use App\Http\Controllers\ApiAcademicController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::group(['prefix' => 'api-academic-program'], function () {
//     Route::get('list', [ApiAcademicController::class, 'index'])->name('api.academic-program.list');
//     Route::post('store', [ApiAcademicController::class, 'store'])->name('api.academic-program.store');
//     Route::get('edit/{id}', [ApiAcademicController::class, 'edit'])->name('api.academic-program.edit');
//     Route::put('update/{id}', [ApiAcademicController::class, 'update'])->name('api.academic-program.update');
//     Route::delete('destroy/{id}', [ApiAcademicController::class, 'destroy'])->name('api.academic-program.destroy');
// });
