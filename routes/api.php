<?php

use App\Http\Controllers\TranslationController;
use Illuminate\Support\Facades\Route;

Route::post('/translate', [TranslationController::class, 'translate'])->name('api.translate');
Route::get('/translation-languages', [TranslationController::class, 'languages'])->name('api.translation-languages');
