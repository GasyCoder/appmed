<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatbotController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// ✅ Routes Chatbot (pas besoin de préfixer par /api/)
Route::post('/chatbot/message', [ChatbotController::class, 'sendMessage'])->middleware('throttle:30,1');
Route::get('/chatbot/history/{sessionId}', [ChatbotController::class, 'getHistory'])->middleware('throttle:60,1');
Route::delete('/chatbot/clear/{sessionId}', [ChatbotController::class, 'clearHistory'])->middleware('throttle:20,1');