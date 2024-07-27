<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use MoonShine\Socialite\Http\Controllers\SocialiteController;

Route::moonshine(static function (): void {
    Route::controller(SocialiteController::class)
        ->prefix('socialite')
        ->as('socialite.')
        ->group(static function (): void {
            Route::get('/{driver}/redirect', 'redirect')->name('redirect');
            Route::get('/{driver}/callback', 'callback')->name('callback');
        });
});
