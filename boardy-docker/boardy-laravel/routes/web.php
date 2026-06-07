<?php

use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\GitHubController;

Route::get('/', function () {
    return redirect()->route('posts.index');
});

Route::resource('posts', PostController::class);

Route::post('/comments', [CommentController::class, 'store'])
    ->middleware('auth')
    ->name('comments.store');

Route::get('/dashboard', function () {
    return redirect()->route('posts.index');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/auth/github', [GitHubController::class, 'redirect'])
    ->name('auth.github');

Route::get('/auth/github/callback', [GitHubController::class, 'callback']);

require __DIR__.'/auth.php';

