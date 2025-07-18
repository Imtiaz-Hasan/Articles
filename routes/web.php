<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Add a simple login route to satisfy the framework
Route::get('/login', function () {
    return response()->json(['message' => 'Login page'], 200);
})->name('login');
