<?php
// ini_set('memory_limit', '298M');

use App\Http\Controllers\API\APIAuthController;
use App\Http\Controllers\API\ArticleController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [ APIAuthController::class, 'login' ])->name('api.login');
Route::post('/register', [ APIAuthController::class, 'register' ])->name('api.register');

Route::get('/articles', [ArticleController::class, 'index'])->name('api.articles');
Route::get('/articles/preferred', [ArticleController::class, 'preferredArticles'])->name('api.user_articles');
Route::get('/articles/sources', [ArticleController::class, 'articlesSources'])->name('api.articles_sources');
Route::get('/articles/{id}', [ArticleController::class, 'show'])->name('api.article_show');

Route::get('/settings', [ APIAuthController::class, 'updateProfile' ])->name('api.update_user');

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('/logout', [ APIAuthController::class, 'logout' ])->name('api.logout');
    Route::get('/me', [ APIAuthController::class, 'authUserDetail' ])->name('api.logged_user');
    // Route::get('/settings', [ APIAuthController::class, 'updateProfile' ])->name('api.update_user');

    Route::prefix('articles')->group(function () {
        // Route::get('/', [ArticleController::class, 'index'])->name('api.articles');
    });
});
