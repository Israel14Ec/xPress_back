<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\GmailController;
use App\Http\Controllers\SendGmail;
use App\Http\Controllers\GmailDesauth;
use App\Http\Controllers\SentMessagesController;
use Illuminate\Support\Facades\Auth;
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
Route::get('/test-broadcast', function () {
    event(new App\Events\Hello('Test message'));
    return 'Event has been sent!';
});
Route::get('/broadcast/test-work-assigned', function () {
    // Datos ficticios para la prueba
    $message = [
        'id' => 1,
        'id_work_order' => 123,
        'to_user' => 3,
        'subject' => 'Nueva orden de trabajo asignada',
        'title' => 'Instalación de equipos',
        'description' => 'Instalación de nuevos equipos en la oficina principal.',
        'instructions' => 'Seguir las instrucciones del manual.',
        'date' => '2024-07-15',
    ];
    $userId = 3;

    // Emitir el evento
    event(new App\Events\WorkAssigned($message, $userId));

    return response()->json(['status' => 'Event broadcasted successfully.']);
});