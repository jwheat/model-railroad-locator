<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClubController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LayoutController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PostController;
use App\Livewire\LayoutEditor;
use App\Livewire\PostEditor;
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
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/stories/new', PostEditor::class)->name('posts.create');
    Route::get('/stories/{post}/edit', PostEditor::class)->name('posts.edit');
});
