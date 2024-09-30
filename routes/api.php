<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\BrandController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\DiscountController;
use App\Http\Controllers\API\OrderController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\ShippingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    // Products
    // Route::apiResource('products', ProductController::class);

    // // Orders
    // Route::apiResource('orders', OrderController::class);
    // Route::get('orders/{id}/track', [OrderController::class, 'track']);

    // // Cart
    // Route::get('cart', [CartController::class, 'index']);
    // Route::post('cart', [CartController::class, 'add']);
    // Route::delete('cart/{id}', [CartController::class, 'remove']);

    // // Coupons
    // Route::post('coupons/apply', [CouponController::class, 'apply']);

    // Users
    Route::get('user/profile', [AuthController::class, 'profile']);

    Route::post('logout', [AuthController::class, 'logout']);

    // Route::post('categories', [CategoryController::class, 'store'])->middleware('admin'); // Admin only
    // Route::put('categories/{id}', [CategoryController::class, 'update'])->middleware('admin'); // Admin only
    // Route::delete('categories/{id}', [CategoryController::class, 'destroy'])->middleware('admin'); // Admin only
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

//CATEGORIES
Route::get('categories', [CategoryController::class, 'index']); // Public access
Route::get('categories/{id}', [CategoryController::class, 'show']); // Public access


// Route::post('categories', [CategoryController::class, 'store'])->middleware('admin'); ; // Admin only
//     Route::put('categories/{id}', [CategoryController::class, 'update'])->middleware('admin'); ; // Admin only
//     Route::delete('categories/{id}', [CategoryController::class, 'destroy'])->middleware('admin'); ; // Admin only

Route::post('categories', [CategoryController::class, 'store']); // Admin only
Route::put('categories/{id}', [CategoryController::class, 'update']); // Admin only
Route::delete('categories/{id}', [CategoryController::class, 'destroy']); // Admin only


//BRANDS
Route::get('brands', [BrandController::class, 'index']); // Public access
Route::get('brands/{id}', [BrandController::class, 'show']); // Public access


// Route::post('brands', [BrandController::class, 'store'])->middleware('admin'); // Admin only
//     Route::put('brands/{id}', [BrandController::class, 'update'])->middleware('admin'); // Admin only
//     Route::delete('brands/{id}', [BrandController::class, 'destroy'])->middleware('admin'); // Admin only

Route::post('brands', [BrandController::class, 'store']); // Admin only
Route::put('brands/{id}', [BrandController::class, 'update']); // Admin only
Route::delete('brands/{id}', [BrandController::class, 'destroy']); // Admin only



//DISCOUNT

Route::get('discounts', [DiscountController::class, 'index']); // Public access
Route::get('discounts/{id}', [DiscountController::class, 'show']); // Public access
Route::post('discounts/apply', [DiscountController::class, 'apply']); // Public access

// Route::post('discounts', [DiscountController::class, 'store'])->middleware('admin'); // Admin only
// Route::put('discounts/{id}', [DiscountController::class, 'update'])->middleware('admin'); // Admin only
// Route::delete('discounts/{id}', [DiscountController::class, 'destroy'])->middleware('admin'); // Admin only


Route::post('discounts', [DiscountController::class, 'store']); // Admin only
Route::put('discounts/{id}', [DiscountController::class, 'update']); // Admin only
Route::delete('discounts/{id}', [DiscountController::class, 'destroy']); // Admin only


//CUSTOMER
Route::get('customer/profile', [CustomerController::class, 'profile']);
Route::put('customer/profile', [CustomerController::class, 'update']);


//ORDERS Authenticate 
Route::post('orders', [OrderController::class, 'create']);
Route::get('orders', [OrderController::class, 'index']);
Route::get('orders/{id}', [OrderController::class, 'show']);
Route::put('orders/{id}', [OrderController::class, 'update']);
Route::delete('orders/{id}', [OrderController::class, 'destroy']);

//PRODUCTS
Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show']);

// Route::post('/products', [ProductController::class, 'create'])->middleware('admin');
// Route::put('/products/{id}', [ProductController::class, 'update'])->middleware('admin');
// Route::delete('/products/{id}', [ProductController::class, 'destroy'])->middleware('admin');
// Admin routes (protected by middleware)
Route::post('/products', [ProductController::class, 'create']);
Route::put('/products/{id}', [ProductController::class, 'update']);
Route::delete('/products/{id}', [ProductController::class, 'destroy']);


//REVIEWS
Route::get('/products/{productId}/reviews', [ReviewController::class, 'index']);

// Customer routes (protected by auth)
Route::post('/products/{productId}/reviews', [ReviewController::class, 'store']);
Route::put('/reviews/{id}', [ReviewController::class, 'update']);
Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);


//SHIPPING
// Protected routes (requires authentication)
Route::post('/orders/{orderId}/shipping', [ShippingController::class, 'store']);
Route::get('/orders/{orderId}/shipping', [ShippingController::class, 'show']);
Route::put('/shippings/{id}', [ShippingController::class, 'update']);
Route::delete('/shippings/{id}', [ShippingController::class, 'destroy']);
