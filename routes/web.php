<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LayoutController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\NotificationUnsubscribeController;
use App\Http\Controllers\PostController;
use App\Livewire\LayoutEditor;
use App\Livewire\NotificationInbox;
use App\Livewire\NotificationPreferences;
use App\Livewire\PostEditor;
use App\Livewire\ProfileEditor;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/layouts', [LayoutController::class, 'index'])->name('layouts.index');
Route::get('/layouts/new', LayoutEditor::class)->middleware('auth')->name('layouts.create');
Route::get('/layouts/{layout}/edit', LayoutEditor::class)->middleware('auth')->name('layouts.edit');
Route::get('/layouts/{layout}', [LayoutController::class, 'show'])->name('layouts.show');

Route::get('/blog', [PostController::class, 'index'])->name('posts.index');
Route::get('/blog/{post}', [PostController::class, 'show'])->name('posts.show');

Route::get('/members/{user}', [MemberController::class, 'show'])->name('members.show');
Route::get('/clubs/{club}', [ClubController::class, 'show'])->name('clubs.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');

    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/stories/new', PostEditor::class)->name('posts.create');
    Route::get('/stories/{post}/edit', PostEditor::class)->name('posts.edit');

    Route::get('/account/notifications', NotificationInbox::class)->name('notifications.index');
    Route::get('/account/notifications/preferences', NotificationPreferences::class)->name('notifications.preferences');
    Route::get('/account/profile', ProfileEditor::class)->name('account.profile');
});

Route::get('/notifications/unsubscribe/{user}/{type}', NotificationUnsubscribeController::class)
    ->middleware('signed')
    ->name('notifications.unsubscribe');
