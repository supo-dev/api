<?php

declare(strict_types=1);

use App\Http\Controllers\FollowController;
use App\Http\Controllers\FollowingFeedController;
use App\Http\Controllers\ForYouFeedController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SessionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserEmailVerificationController;
use Illuminate\Support\Facades\Route;

// Sessions...
Route::post('/sessions', [SessionController::class, 'store'])->name('sessions.store');

// Users...
Route::post('/users', [UserController::class, 'store'])->name('users.store');

Route::middleware(['auth:sanctum'])->group(function (): void {

    // Sessions...
    Route::get('/sessions', [SessionController::class, 'show'])->name('sessions.show');
    Route::delete('/sessions', [SessionController::class, 'destroy'])->name('sessions.destroy');

    // Users...
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users', [UserController::class, 'destroy'])->name('users.destroy');

    // Posts...
    Route::get('/posts/{post}', [PostController::class, 'show'])->name('posts.show');
    Route::post('/posts', [PostController::class, 'store'])->name('posts.store');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // Feeds...
    Route::get('/feeds/for-you', ForYouFeedController::class)->name('feeds.for-you')
        ->withoutMiddleware('auth:sanctum');
    Route::get('/feeds/following', FollowingFeedController::class)->name('feeds.following');

    // Follows...
    Route::post('/follows/{user}', [FollowController::class, 'store'])->name('follows.store');
    Route::delete('/follows/{user}', [FollowController::class, 'destroy'])->name('follows.destroy');

    // Blocks
    Route::post('/blocks/{user}', [App\Http\Controllers\BlockController::class, 'store'])->name('blocks.store');
    Route::delete('/blocks/{user}', [App\Http\Controllers\BlockController::class, 'destroy'])->name('blocks.destroy');

    // Likes...
    Route::post('/likes/{post}', [LikeController::class, 'store'])->name('likes.store');
    Route::delete('/likes/{post}', [LikeController::class, 'destroy'])->name('likes.destroy');

    // Email Verification...
    Route::post('/email/send-verification', [UserEmailVerificationController::class, 'show'])->name('email.send-verification');
    Route::post('/email/verify', [UserEmailVerificationController::class, 'update'])->name('email.verify');
});
