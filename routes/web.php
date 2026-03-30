<?php

use App\Http\Controllers\TranslationController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');
Route::get('/translator', [TranslationController::class, 'index'])->name('translator.index');
Route::view('video-chat', 'video-chat')->name('video-chat');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
