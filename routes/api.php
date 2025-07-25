<?php
// ini_set('memory_limit', '720M');
// ini_set('memory_limit', '298M');

use App\Http\Controllers\API\APIAuthController;
use App\Http\Controllers\API\ArticleController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [ APIAuthController::class, 'login' ])->name('api.login');
Route::post('/register', [ APIAuthController::class, 'register' ])->name('api.register');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [ APIAuthController::class, 'logout' ])->name('api.logout');
    Route::get('/me', [ APIAuthController::class, 'authUserDetail' ])->name('api.logged_user');

    Route::post('/user-settings', [ APIAuthController::class, 'updateProfile' ])->name('api.update_user');

    Route::prefix('articles')->group(function () {
        Route::get('/', [ArticleController::class, 'index'])->name('api.articles');
        Route::get('/preferred', [ArticleController::class, 'preferredArticles'])->name('api.user_articles');
        Route::get('/sources', [ArticleController::class, 'articlesSources'])->name('api.articles_sources');
        Route::get('/{id}', [ArticleController::class, 'show'])->name('api.article_show');
    });
});
