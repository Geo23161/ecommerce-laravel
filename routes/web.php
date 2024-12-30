<?php

use Illuminate\Support\Facades\Route;

use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home'); 
});

Route::get('/login', function () {
    return Inertia::render('LoginPage'); 
});

Route::get('/register', function () {
    return Inertia::render('RegisterPage'); 
});

Route::get('/buyer', function () {
    return Inertia::render('HomeBuyer'); 
});

Route::get('/vendor', function () {
    return Inertia::render('HomeSeller'); 
});

Route::get('/add-prod', function () {
    return Inertia::render('ProductForm'); 
});
