<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\JWTAuthController;
use App\Http\Controllers\ProduitController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Middleware\JwtMiddleware;

Route::post( 'register' , [JWTAuthController:: class , 'register' ]); 
Route::post( 'login' , [JWTAuthController:: class , 'login' ]); 
Route::post('refresh-token', [JWTAuthController::class, 'refreshToken']);

Route::middleware([JwtMiddleware:: class ])->group(function () { 
    Route::get( 'user' , [JWTAuthController:: class , 'getUser' ]); 
    Route::post( 'logout' , [JWTAuthController:: class , 'logout' ]);
 });
 

 Route::middleware([JwtMiddleware:: class ])->group(function () {
    
    Route::get('produits', [ProduitController::class, 'index']); 
    Route::get('produits/{id}', [ProduitController::class, 'show']); 

    
    Route::post('produits', [ProduitController::class, 'store']); 
    Route::put('produits/{id}', [ProduitController::class, 'update']); 
    Route::delete('produits/{id}', [ProduitController::class, 'destroy']); 
});

Route::middleware([JwtMiddleware:: class ])->group(function () {
    Route::get('cart', [CartController::class, 'index'])->name('cart.index');
    // Route::post('cart/ordered', [CartController::class, 'updateOrder'])->name('cart.updateOrder');
    Route::get('cart/ordered', [CartController::class, 'getOrderedItems'])->name('cart.getOrderedItems');
    Route::post('cart/add', [CartController::class, 'store'])->name('cart.store');
    Route::patch('cart/update/{id}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('cart/remove/{id}', [CartController::class, 'destroy'])->name('cart.destroy');
});

Route::middleware([JwtMiddleware:: class ])->group(function () {
    // Get all orders for the authenticated user
    Route::get('orders', [OrderController::class, 'index']);

    // Create a new order
    Route::post('orders', [OrderController::class, 'store']);
});