<?php

use Illuminate\Support\Facades\Route;
use laravel\Socialite\Facades\Socialite;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth/github', function () {
    $scopes = ['repo'];
    return Socialite::driver('github')->scopes($scopes)->redirect();
});