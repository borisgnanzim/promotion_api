<?php

use App\Http\Controllers\API\Auth\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\ImageController;
use App\Http\Controllers\API\PromotionController;
use App\Http\Controllers\API\StoreController;
use App\Http\Controllers\API\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('auth')->group(function () {

    Route::middleware('auth:sanctum')->group(function () {
        // Route::get('/user', function (Request $request) {
        //     return $request->user();
        // });
        Route::post('logout', [AuthController::class, 'logout']);
        Route::get('profile', [AuthController::class, 'profile']);
    });

    Route::post('login', [AuthController::class, 'login']);
    // login pour admin
    Route::post('admin/login', [AuthController::class, 'loginAdmin']);
});

Route::middleware(['auth:sanctum','ability:admin'])->prefix('admin')->group( function() {
    Route::prefix('categories')->group(function (){
        Route::get('/', [CategoryController::class, 'index']);
        Route::post('/', [CategoryController::class, 'store']);
        Route::get('/{ref}', [CategoryController::class, 'show']);
        Route::put('/{ref}', [CategoryController::class, 'update']);
        Route::delete('/{ref}', [CategoryController::class, 'destroy']);
    });

    Route::prefix('images')->group( function () {
        Route::get('/', [ImageController::class,'index']);
        Route::get('/{ref}', [ImageController::class,'show']);
        Route::post('/', [ImageController::class,'store']);
        Route::delete('/{ref}', [ImageController::class,'destroy']);
    });

    Route::prefix('users')->group(function (){
        Route::get('/', [UserController::class, 'index']);
        Route::get('/{ref}', [UserController::class, 'show']);
        Route::post('/', [UserController::class, 'store']);
        Route::put('/{ref}', [UserController::class, 'update']);
        Route::delete('/{ref}', [UserController::class, 'destroy']);
    });

    Route::prefix('promotions')->group( function(){
        Route::get('/', [PromotionController::class,'index']);
        //Route::get('/actives', [PromotionController::class,'activePromotions']);
        //Route::get('/upcoming', [PromotionController::class,'upcomingPromotions']);
        Route::get('/{ref}', [PromotionController::class,'show']);
        Route::post('/', [PromotionController::class,'store']);
        Route::put('/{ref}', [PromotionController::class,'update']);
        Route::delete('/{ref}', [PromotionController::class,'destroy']);
    });

    Route::prefix('stores')->group(function(){
        Route::get('/', [StoreController::class,'index']);
        Route::get('/{ref}', [StoreController::class,'show']);
        Route::post('/', [StoreController::class,'store']);
        Route::put('/{ref}', [StoreController::class,'update']);
        Route::delete('/{ref}', [StoreController::class,'destroy']);
       // Route::get('/{store_ref}/items',[StoreController::class, 'getItemsByStore']);
    });
});