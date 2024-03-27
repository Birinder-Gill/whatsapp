<?php

use App\Http\Controllers\WhatsAppMessageController;
use App\Http\Middleware\KillSwitchMiddleware;
use App\Http\Middleware\LanguageDetection;
use App\Http\Middleware\LogAllMessagesMiddleware;
use App\Http\Middleware\MessageLoggerMiddleware;
use App\Http\Middleware\OpenAiTokenCheckMiddleware;
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
Route::middleware([LanguageDetection::class])->group(function () {
    Route::get('/sendMedia', [WhatsAppMessageController::class, 'sendMediaApi']);

    // Route::get('/test', [WhatsAppMessageController::class, 'isAskingForPrice'])->middleware([KillSwitchMiddleware::class,MessageLoggerMiddleware::class]);
    Route::get('/sendMessage', [WhatsAppMessageController::class, 'sendMessage']);//->middleware([MessageLoggerMiddleware::class, KillSwitchMiddleware::class,]);;
    Route::post('/mickeyCalling', [WhatsAppMessageController::class, 'mickeyCalling']);
    Route::post('/orderReceived', [WhatsAppMessageController::class, 'orderReceived']);
    Route::get('/generateImage', [WhatsAppMessageController::class, 'generateImage']);
    Route::get('/makeSubs', [WhatsAppMessageController::class, 'makeSubs']);

});
Route::post('/messageReceived', [WhatsAppMessageController::class, 'messageReceived'])->middleware([LogAllMessagesMiddleware::class, KillSwitchMiddleware::class,MessageLoggerMiddleware::class]);
Route::get('/testReceived', [WhatsAppMessageController::class, 'testReceived']);
Route::get('/getAiService', [WhatsAppMessageController::class, 'getAiService'])->middleware([OpenAiTokenCheckMiddleware::class]);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
