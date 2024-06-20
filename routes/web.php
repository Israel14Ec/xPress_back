<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\GmailController;
use App\Http\Controllers\SendGmail;
use App\Http\Controllers\GmailDesauth;
use App\Http\Controllers\SentMessagesController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('api/google/auth', [GoogleAuthController::class, 'redirectToGoogle']);
Route::get('google/auth/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
Route::get('api/gmail/list-messages', [GmailController::class, 'listMessages']);
Route::post('/api/gmail/send-reply', [SendGmail::class, 'sendMessage']);
Route::get('/api/gmail/logout', [GmailDesauth::class, 'logout']);
Route::get('/api/gmail/sent-messages', [SentMessagesController::class, 'listSentMessages']);