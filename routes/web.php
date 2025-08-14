<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('pl', function () {
    return view('pljourney');
});

Route::get('sf-pl', function () {
    return view('sf-pljourney');
});