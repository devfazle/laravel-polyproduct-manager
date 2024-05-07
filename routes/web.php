<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\FrontController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SizeController;
use App\Http\Controllers\SliderController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

//below is general routes

Route::group([],function () {
    Route::get('/shop', [FrontController::class, 'shop'])->name('shop');
    Route::get('/cart', [FrontController::class, 'cart'])->name('cart');
    Route::get('/blog', [FrontController::class, 'blog'])->name('blog');
    Route::get('/about', [FrontController::class, 'about'])->name('about');
    Route::get('/contact', [FrontController::class, 'contact'])->name('contact');
    Route::get('/blogdetails', [FrontController::class, 'bdetails'])->name('bdetails');
    Route::get('/game', [FrontController::class, 'game'])->name('game');
    Route::get('/', [FrontController::class, 'index'])->name('home');
});

//Below admin routes

Route::prefix('admin')->group(function () {
    Route::resource('/slider', SliderController::class)->names('slide');
    Route::resource('/category', CategoryController::class)->names('cat');
    Route::resource('/color', ColorController::class)->names('color');
    Route::resource('/tag', TagController::class)->names('tag');
    Route::resource('/size', SizeController::class)->names('size');
    Route::resource('/product', ProductController::class)->names('product');
    Route::get('/dash', [AdminController::class, 'dash'])->name('dash');
});

