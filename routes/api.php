<?php


/*
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientCheckController;
use App\Http\Controllers\UpdatePocketIdController;
use App\Http\Controllers\SecurePageController;

// Гостевые маршруты
Route::post('/register', [UserController::class, 'store']);
Route::post('/login', [UserController::class, 'loginAuth']);
Route::post('/forgot-password', [UserController::class, 'forgotPasswordStore']);
Route::post('/reset-password', [UserController::class, 'resetPasswordUpdate']);

// Защищённые маршруты
Route::middleware(['auth:sanctum','ensure.email.verified'])->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    Route::get('/secure-page', [ClientCheckController::class, 'securePage'])
        ->middleware('ensure.client.deposit');
    Route::post('/update-pocket-id', [UpdatePocketIdController::class, 'update']);
    Route::post('/change-email', [UserController::class, 'changeEmail']);
});

// API-роуты
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return response()->json([
            'id' => $request->user()->id,
            'email' => $request->user()->email,
            'pocket_id' => $request->user()->pocket_id,
        ]);
    });

    Route::get('/check-access', [SecurePageController::class, 'checkAccess']);
});
*/



use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UpdatePocketIdController;
use App\Http\Controllers\SecurePageController;

// Гостевые маршруты
Route::post('/register', [UserController::class, 'store']);
Route::post('/login', [UserController::class, 'loginAuth']);
Route::post('/forgot-password', [UserController::class, 'forgotPasswordStore']);
Route::post('/reset-password', [UserController::class, 'resetPasswordUpdate']);

// Защищённые маршруты — все под middleware auth:sanctum
Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [UserController::class, 'logout']);
    
    // Только если email верифицирован
    Route::middleware(['verified'])->group(function () {
       

    //    Route::post('/update-pocket-id', [UpdatePocketIdController::class, 'update']);
        Route::post('/change-email', [UserController::class, 'changeEmail']);
    });

    // Дополнительные API маршруты
    Route::get('/user', function (Request $request) {
        return response()->json([
            'id' => $request->user()->id,
            'email' => $request->user()->email,
            'pocket_id' => $request->user()->pocket_id,
        ]);
    });


    Route::post('/update-pocket-id', [UpdatePocketIdController::class, 'update']);


    Route::get('/check-access', [SecurePageController::class, 'checkAccess']);
});


//Route::post('/update-pocket-id', [UpdatePocketIdController::class, 'update']);
