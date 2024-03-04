<?php

use App\Http\Controllers\WhatsAppMessageController;
use App\Http\Middleware\LanguageDetection;
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

//REQUEST GOES TO SERVICE CONSTRUCTOR THAN TO MIDDLEWARE.
Route::middleware([LanguageDetection::class])->group(function (){
    Route::get('/sendMedia', [WhatsAppMessageController::class, 'sendMediaApi']);
    Route::get('/test', [WhatsAppMessageController::class, 'isAskingForPrice']);
    Route::get('/sendMessage', [WhatsAppMessageController::class, 'sendMessage']);
    Route::post('/messageReceived', [WhatsAppMessageController::class, 'messageReceived']);
    Route::post('/mickeyCalling', [WhatsAppMessageController::class, 'mickeyCalling']);
    Route::get('/generateImage', [WhatsAppMessageController::class, 'generateImage']);
    Route::post('/orderReceived', [WhatsAppMessageController::class, 'orderReceived']);

});


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

