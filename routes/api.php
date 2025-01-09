<?php


use Illuminate\Support\Facades\Route;


Route::post('/login',[\App\Http\Controllers\AdminController::class,'login']);

//后台管理
//middleware(['auth:sanctum'])->
Route::prefix('admin')->group(function () {
    Route::get('/settings',[\App\Http\Controllers\SettingController::class,'index']);
    Route::post('/settings',[\App\Http\Controllers\SettingController::class,'update']);
//    Route::post('/category',[\App\Http\Controllers\CategoryController::class,'store']);
//    Route::get('/category',[\App\Http\Controllers\CategoryController::class,'index']);
});

//Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
//    return $request->user();
//});





Route::apiResource('category', \App\Http\Controllers\CategoryController::class);
Route::apiResource('product', \App\Http\Controllers\ProductController::class);
Route::apiResource('giftCode', \App\Http\Controllers\GiftCodeController::class);
Route::post('/giftCode/import', [\App\Http\Controllers\GiftCodeController::class, 'import']);

Route::apiResource('order', \App\Http\Controllers\OrderController::class);

Route::post('/orders', [App\Http\Controllers\IndexController::class, 'createOrder']);
Route::get('/search', [App\Http\Controllers\IndexController::class, 'search']);

Route::get('/index',[\App\Http\Controllers\IndexController::class,'index']);
Route::get('/pay',[\App\Http\Controllers\IndexController::class,'getPay']);
Route::get('/notify',[\App\Http\Controllers\IndexController::class,'notify']);



