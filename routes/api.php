<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ActRuleFormController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\StateController;
use App\Http\Controllers\Api\GazetteNortificationController;
use App\Http\Controllers\Api\HolidayController;

// Health check
Route::get('/ping', fn() => ['message' => 'API working']);

// ActRuleForm APIs
Route::prefix('act-rule-forms')->group(function () {
    Route::get('/', [ActRuleFormController::class, 'index']);
    Route::get('/states', [ActRuleFormController::class, 'states']);
    Route::get('/{actRuleForm:slug}', [ActRuleFormController::class, 'show']); // ✅
});

Route::prefix('posts')->group(function () {
    Route::get('/', [PostController::class, 'index']);              // list
    Route::get('/{post:slug}', [PostController::class, 'show']);   // detail by slug
});
Route::get('/states', [StateController::class, 'index']);

Route::get('/gazettes', [GazetteNortificationController::class, 'index']);      // list + filters + pagination
Route::get('/gazettes/{slug}', [GazetteNortificationController::class, 'show']);

Route::get('/holidays', [HolidayController::class, 'index']);       // state + slug list
Route::get('/holidays/{slug}', [HolidayController::class, 'show']);  // full detail by slug