<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MobilePropertyController;
use App\Http\Controllers\Api\FavoriteController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\PropertyController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::prefix('v1')->group(function () {
    // Authentication
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    // Public property endpoints
    Route::get('/properties', [MobilePropertyController::class, 'index']);
    Route::get('/properties/featured', [MobilePropertyController::class, 'featured']);
    Route::get('/properties/{id}', [MobilePropertyController::class, 'show']);

    // Map API endpoints (existing)
    Route::prefix('map')->group(function () {
        Route::get('/properties', [PropertyController::class, 'geojson']);
        Route::get('/search/radius', [PropertyController::class, 'searchByRadius']);
        Route::get('/search/area', [PropertyController::class, 'searchByArea']);
        Route::get('/properties/{property}/nearby', [PropertyController::class, 'nearby']);
        Route::get('/geocode', [PropertyController::class, 'geocode']);
        Route::get('/reverse-geocode', [PropertyController::class, 'reverseGeocode']);
        Route::get('/statistics', [PropertyController::class, 'statistics']);
        Route::get('/clusters', [PropertyController::class, 'clusters']);
    });
});

// Protected routes (require authentication)
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    // Authentication
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/email/resend-verification', [AuthController::class, 'resendVerification']);
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);
    Route::put('/user/password', [AuthController::class, 'changePassword']);

    // Properties (landlord)
    Route::post('/properties', [MobilePropertyController::class, 'store']);
    Route::put('/properties/{id}', [MobilePropertyController::class, 'update']);
    Route::delete('/properties/{id}', [MobilePropertyController::class, 'destroy']);
    Route::get('/my-properties', [MobilePropertyController::class, 'myProperties']);

    // Favorites
    Route::get('/favorites', [FavoriteController::class, 'index']);
    Route::post('/favorites/{propertyId}', [FavoriteController::class, 'store']);
    Route::delete('/favorites/{propertyId}', [FavoriteController::class, 'destroy']);
    Route::get('/favorites/{propertyId}/check', [FavoriteController::class, 'check']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread', [NotificationController::class, 'unread']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/notifications/{id}', [NotificationController::class, 'destroy']);

    // Push notifications
    Route::post('/push-tokens', [NotificationController::class, 'registerToken']);
    Route::delete('/push-tokens', [NotificationController::class, 'unregisterToken']);

    // Notification settings
    Route::get('/notification-settings', [NotificationController::class, 'settings']);
    Route::put('/notification-settings', [NotificationController::class, 'updateSettings']);
});
