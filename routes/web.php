<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HelloController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\EditProfileController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RestaurantController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('/');

//Route::get('/restaurants', function () {
//    return Inertia::render('Restaurants');
//})->name('restaurants');

Route::get('home', [HomeController::class, 'index'])->name('home');
//Route::get('restaurants', [RestaurantController::class, 'index'])->name('restaurants');
Route::resource('employee', EmployeeController::class)->only(['index', 'store', 'update', 'destroy']);
Route::resource('restaurants', RestaurantController::class)->only(['index', 'store', 'update', 'destroy']);
Route::resource('products', ProductController::class)->only(['index', 'store', 'update', 'destroy']);
Route::resource('users', UserController::class)->only(['index', 'update', 'destroy']);
Route::resource('cart', CartController::class)->only(['index', 'destroy']);
Route::resource('editProfile', EditProfileController::class)->only(['index', 'update']);
Route::get('orders', [OrderController::class, 'index'])->name('orders');

Route::post('addToCart', [ProductController::class, 'addToCart'])->name('addToCart');
Route::post('updateUserData', [UserController::class, 'updateUserData'])->name('updateUserData');
Route::post('orderCartItems', [CartController::class, 'orderCartItems'])->name('orderCartItems');

require __DIR__ . '/auth.php';
