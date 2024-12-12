<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminLogController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CartItemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DeliveryController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProductReviewController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImageController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserAddressController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\auth\RegisteredUserController;
use App\Http\Controllers\UserProfileImageController;
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


// AdminLog Routes
Route::apiResource('admin-logs', AdminLogController::class);

// Cart Routes
Route::apiResource('carts', CartController::class);

// CartItem Routes
Route::apiResource('cart-items', CartItemController::class);

// Category Routes
Route::apiResource('categories', CategoryController::class);

// Delivery Routes
Route::apiResource('deliveries', DeliveryController::class);

// Discount Routes
Route::apiResource('discounts', DiscountController::class);

// Notification Routes
Route::apiResource('notifications', NotificationController::class);

// Order Routes
Route::apiResource('orders', OrderController::class);

// Payment Routes
Route::apiResource('payments', PaymentController::class);

// ProductReview Routes
Route::apiResource('product-reviews', ProductReviewController::class);

// Product Routes
Route::apiResource('products', ProductController::class);

// ProductImage Routes
Route::middleware('auth:sanctum')->post('products/{productId}/image', [ProductImageController::class, 'store']); // Upload product image
Route::get('products/{productId}/images', [ProductImageController::class, 'show']);  // Get product images

// UserProfileImage Routes
Route::middleware('auth:sanctum')->post('profile-image', [UserProfileImageController::class, 'store']); // Upload profile image
Route::middleware('auth:sanctum')->get('profile-image', [UserProfileImageController::class, 'show']);  // Get profile image

// Role Routes
Route::apiResource('roles', RoleController::class);

// UserAddress Routes
Route::apiResource('user-addresses', UserAddressController::class);

// Users Routes (for registration, login, profile, etc.)
Route::middleware('auth:sanctum')->get('/user', [UserController::class, 'index']);
Route::middleware('auth:sanctum')->get('/user/{Id}', [UserController::class, 'show']);
Route::middleware('auth:sanctum')->put('/user/{Id}', [UserController::class, 'update']);
Route::middleware('auth:sanctum')->delete('/user/{Id}', [UserController::class, 'destroy']);
// Route::apiResource('/user', UserController::class);
Route::post('/user/register', [UserController::class, 'register']);
Route::post('/register', [RegisteredUserController::class, 'register']);
Route::post('/user/login', [UserController::class, 'login'])->name('login');
Route::middleware('auth:sanctum')->get('profile', [UserController::class, 'profile']);
Route::middleware('auth:sanctum')->post('logout', [UserController::class, 'logout']);

