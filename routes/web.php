<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\EmailPreferenceController;
use App\Http\Controllers\HomepageController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\LandlordProfileController;
use Illuminate\Support\Facades\Route;

// Public routes (no authentication required)
Route::get('/', [HomepageController::class, 'index'])->name('home');
Route::get('/homepage', [HomepageController::class, 'index'])->name('homepage.index');
Route::get('/homepage/search', [HomepageController::class, 'search'])->name('homepage.search');
Route::get('/homepage/search-suggestions', [HomepageController::class, 'getSearchSuggestions'])->name('homepage.search-suggestions');

// Legacy public routes (keeping for backward compatibility)
Route::get('/legacy', [PublicController::class, 'index'])->name('public.home');
Route::get('/listings', [PublicController::class, 'properties'])->name('public.properties');
// Route::get('/listings/{property}', [PublicController::class, 'show'])->name('public.property.show'); // Commented out due to conflict
Route::get('/browse', [SearchController::class, 'index'])->name('public.search');

// Unsubscribe route (no auth required)
Route::get('/unsubscribe/{user}', [EmailPreferenceController::class, 'unsubscribe'])->name('email.unsubscribe');

// Legal pages (no auth required)
Route::get('/terms', [LegalController::class, 'terms'])->name('legal.terms');
Route::get('/privacy', [LegalController::class, 'privacy'])->name('legal.privacy');
Route::get('/cookies', [LegalController::class, 'cookies'])->name('legal.cookies');

// Team pages (no auth required)
Route::get('/team', [TeamController::class, 'index'])->name('team.index');
Route::get('/team/{id}', [TeamController::class, 'show'])->name('team.show');

// Landlord profiles (publicly viewable)
Route::get('/landlords/{user}/{slug?}', [LandlordProfileController::class, 'show'])->name('landlords.show');

// Blog pages (no auth required)
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/blog/category/{slug}', [BlogController::class, 'category'])->name('blog.category');

// Public Properties pages (no auth required)
Route::get('/listings', [App\Http\Controllers\PublicPropertiesController::class, 'index'])->name('properties.public.index');
Route::get('/listings/search', [App\Http\Controllers\PublicPropertiesController::class, 'search'])->name('properties.public.search');
Route::get('/listings-map', [App\Http\Controllers\PublicPropertiesController::class, 'map'])->name('properties.public.map');
Route::get('/debug-map', function() { return view('debug-map'); });
Route::get('/listings/{id}', [App\Http\Controllers\PublicPropertiesController::class, 'show'])->name('properties.public.show');
Route::get('/api/properties/suggestions', [App\Http\Controllers\PublicPropertiesController::class, 'getSuggestions'])->name('properties.suggestions');


// Search and comparison routes
Route::get('/search/suggestions', [SearchController::class, 'getSuggestions'])->name('search.suggestions');
Route::get('/comparison', [SearchController::class, 'showComparison'])->name('comparison.show');

Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])->name('dashboard');

// Email verification routes
Route::middleware(['auth'])->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'send'])
        ->middleware('rate_limit.email_verification')
        ->name('verification.send');
    Route::get('/email/verify/{id}/{token}', [EmailVerificationController::class, 'verify'])->name('verification.verify');
    Route::post('/email/resend', [EmailVerificationController::class, 'resend'])
        ->middleware('rate_limit.email_verification')
        ->name('verification.resend');
});

// Password reset routes
Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [PasswordResetController::class, 'showRequestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])
        ->middleware('rate_limit.password_reset')
        ->name('password.email');
    Route::get('/reset-password/{token}/{email}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

// Properties routes - accessible by all authenticated and verified users
Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('properties', PropertyController::class)
        ->middleware('rate_limit.property_management');
    
    Route::get('/search', [SearchController::class, 'index'])->name('properties.search');
    Route::get('/map', [SearchController::class, 'map'])->name('properties.map');
    Route::get('/search-map', [SearchController::class, 'searchMap'])->name('properties.search-map');
    Route::get('/mobile-search', [SearchController::class, 'mobileSearch'])->name('properties.mobile-search');
    
    // Dashboard routes
    Route::get('/landlord/dashboard', [App\Http\Controllers\LandlordDashboardController::class, 'index'])->name('landlord.dashboard');
    Route::get('/renter/dashboard', [App\Http\Controllers\RenterDashboardController::class, 'index'])->name('renter.dashboard');
    
    // Advanced search routes
    Route::post('/search/save', [SearchController::class, 'saveSearch'])->name('search.save');
    Route::get('/search/saved/{savedSearch}', [SearchController::class, 'loadSavedSearch'])->name('search.saved.load');
    Route::delete('/search/saved/{savedSearch}', [SearchController::class, 'deleteSavedSearch'])->name('search.saved.delete');
    
    // Property comparison routes
    Route::post('/properties/{property}/compare', [SearchController::class, 'addToComparison'])->name('properties.compare.add');
    Route::delete('/properties/{property}/compare', [SearchController::class, 'removeFromComparison'])->name('properties.compare.remove');
    
    // Image routes
    Route::post('/properties/{property}/images', [ImageController::class, 'store'])
        ->middleware('rate_limit.property_management')
        ->name('images.store');
    Route::delete('/images/{image}', [ImageController::class, 'destroy'])->name('images.destroy');
    Route::patch('/images/{image}/primary', [ImageController::class, 'setPrimary'])->name('images.primary');
    
    // Message routes
    Route::resource('messages', MessageController::class)->only(['index', 'show']);
    Route::get('/properties/{property}/message', [MessageController::class, 'create'])->name('messages.create');
    Route::post('/properties/{property}/message', [MessageController::class, 'store'])
        ->middleware('rate_limit.messaging')
        ->name('messages.store');
    
    // Enhanced report routes (must come before resource routes)
    Route::get('/reports/my-reports', [ReportController::class, 'myReports'])->name('reports.my-reports');
    Route::get('/reports/notifications/count', [ReportController::class, 'getUnreadCount'])->name('reports.notifications.count');
    Route::post('/reports/{report}/comment', [ReportController::class, 'addComment'])->name('reports.comment');
    Route::post('/reports/{report}/follow-up', [ReportController::class, 'requestFollowUp'])->name('reports.follow-up');
    Route::post('/reports/{report}/mark-read', [ReportController::class, 'markNotificationsRead'])->name('reports.mark-read');
    
    // Message reporting routes
    Route::get('/messages/{message}/report', [App\Http\Controllers\MessageReportController::class, 'create'])->name('message-reports.create');
    Route::post('/messages/{message}/report', [App\Http\Controllers\MessageReportController::class, 'store'])->name('message-reports.store');
    Route::get('/message-reports/my-reports', [App\Http\Controllers\MessageReportController::class, 'myReports'])->name('message-reports.my-reports');
    Route::get('/message-reports/{messageReport}', [App\Http\Controllers\MessageReportController::class, 'show'])->name('message-reports.show');
    Route::post('/message-reports/{messageReport}/comment', [App\Http\Controllers\MessageReportController::class, 'addComment'])->name('message-reports.comment');
    Route::post('/message-reports/{messageReport}/follow-up', [App\Http\Controllers\MessageReportController::class, 'requestFollowUp'])->name('message-reports.follow-up');
    Route::post('/message-reports/{messageReport}/mark-read', [App\Http\Controllers\MessageReportController::class, 'markNotificationsRead'])->name('message-reports.mark-read');
    Route::get('/message-reports/notifications/count', [App\Http\Controllers\MessageReportController::class, 'getUnreadCount'])->name('message-reports.notifications.count');

    // Unified notifications routes
    Route::post('/notifications/{type}/{id}/mark-read', [NotificationsController::class, 'markRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationsController::class, 'markAllRead'])->name('notifications.mark-all-read');
    
    // Report routes
    Route::resource('reports', ReportController::class)->only(['index', 'create', 'store', 'show']);
    Route::get('/reports/create/property/{property}', [ReportController::class, 'create'])->name('reports.create.property');
    Route::get('/reports/create/user/{user}', [ReportController::class, 'create'])->name('reports.create.user');
    Route::get('/reports/create/message/{message}', [ReportController::class, 'create'])->name('reports.create.message');
    Route::post('/messages/{message}/reply', [MessageController::class, 'reply'])
        ->middleware('rate_limit.messaging')
        ->name('messages.reply');
    
    // Enhanced Property routes (comparison only - favorites already exist)
    Route::post('/properties/{property}/compare', [PropertyController::class, 'addToComparison'])->name('properties.compare.add');
    Route::delete('/properties/{property}/compare', [PropertyController::class, 'removeFromComparison'])->name('properties.compare.remove');
    Route::get('/properties/compare', [PropertyController::class, 'compare'])->name('properties.compare');
    
    // Existing Favorite routes
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/properties/{property}/favorite', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/properties/{property}/favorite', [FavoriteController::class, 'destroy'])->name('favorites.destroy');
    Route::patch('/properties/{property}/favorite', [FavoriteController::class, 'update'])->name('favorites.update');
    Route::post('/favorites/wishlist', [FavoriteController::class, 'createWishlist'])->name('favorites.wishlist.create');
    Route::get('/favorites/wishlist', [FavoriteController::class, 'getWishlist'])->name('favorites.wishlist.get');
    
    // Review routes
    Route::get('/properties/{property}/reviews', [ReviewController::class, 'index'])->name('reviews.index');
    Route::get('/properties/{property}/reviews/create', [ReviewController::class, 'create'])->name('reviews.create');
    Route::post('/properties/{property}/reviews', [ReviewController::class, 'store'])->name('reviews.store');
    Route::get('/reviews/{review}', [ReviewController::class, 'show'])->name('reviews.show');
    Route::get('/reviews/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
    Route::patch('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    
    // Email preferences
    Route::get('/email-preferences', [EmailPreferenceController::class, 'index'])->name('email.preferences');
    Route::put('/email-preferences', [EmailPreferenceController::class, 'update'])->name('email.preferences.update');
});

// Admin only routes
Route::middleware(['auth', 'verified', 'role:admin', 'rate_limit.admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::patch('/users/{user}/status', [AdminController::class, 'updateUserStatus'])->name('admin.users.status');
    Route::get('/pending-properties', [AdminController::class, 'pendingProperties'])->name('admin.pending-properties');
    Route::patch('/properties/{property}/approve', [AdminController::class, 'approveProperty'])->name('admin.properties.approve');
    Route::patch('/properties/{property}/reject', [AdminController::class, 'rejectProperty'])->name('admin.properties.reject');
    Route::get('/properties', [AdminController::class, 'allProperties'])->name('admin.properties.index');
    Route::patch('/properties/{property}/priority', [AdminController::class, 'updatePropertyPriority'])->name('admin.properties.priority');
    
    // Property update approval routes
    Route::get('/properties/pending-updates', [AdminController::class, 'pendingUpdates'])->name('admin.properties.pending-updates');
    Route::post('/properties/{property}/approve-update', [AdminController::class, 'approveUpdate'])->name('admin.properties.approve-update');
    Route::post('/properties/{property}/reject-update', [AdminController::class, 'rejectUpdate'])->name('admin.properties.reject-update');
    Route::get('/pending-reviews', [AdminController::class, 'pendingReviews'])->name('admin.pending-reviews');
    Route::patch('/reviews/{review}/approve', [ReviewController::class, 'approve'])->name('admin.reviews.approve');
    Route::patch('/reviews/{review}/reject', [ReviewController::class, 'reject'])->name('admin.reviews.reject');
    
    // Report management routes
    Route::get('/reports', [App\Http\Controllers\Admin\ReportManagementController::class, 'index'])->name('admin.reports.index');
    Route::get('/reports/{report}', [App\Http\Controllers\Admin\ReportManagementController::class, 'show'])->name('admin.reports.show');
    Route::patch('/reports/{report}', [App\Http\Controllers\Admin\ReportManagementController::class, 'update'])->name('admin.reports.update');
    Route::post('/reports/{report}/resolve', [App\Http\Controllers\Admin\ReportManagementController::class, 'resolve'])->name('admin.reports.resolve');
    Route::post('/reports/bulk-action', [App\Http\Controllers\Admin\ReportManagementController::class, 'bulkAction'])->name('admin.reports.bulk-action');
    
    // Featured properties management routes
    Route::get('/featured-properties', [App\Http\Controllers\Admin\FeaturedPropertyController::class, 'index'])->name('admin.featured-properties.index');
    Route::post('/featured-properties/{property}/feature', [App\Http\Controllers\Admin\FeaturedPropertyController::class, 'feature'])->name('admin.featured-properties.feature');
    Route::post('/featured-properties/{property}/unfeature', [App\Http\Controllers\Admin\FeaturedPropertyController::class, 'unfeature'])->name('admin.featured-properties.unfeature');
    Route::post('/featured-properties/bulk-feature', [App\Http\Controllers\Admin\FeaturedPropertyController::class, 'bulkFeature'])->name('admin.featured-properties.bulk-feature');
    Route::post('/featured-properties/bulk-unfeature', [App\Http\Controllers\Admin\FeaturedPropertyController::class, 'bulkUnfeature'])->name('admin.featured-properties.bulk-unfeature');
    Route::get('/featured-properties/analytics', [App\Http\Controllers\Admin\FeaturedPropertyController::class, 'analytics'])->name('admin.featured-properties.analytics');
    
    // Enhanced admin report routes
    Route::post('/reports/{report}/comment', [App\Http\Controllers\Admin\ReportManagementController::class, 'addComment'])->name('admin.reports.comment');
    Route::patch('/reports/{report}/status', [App\Http\Controllers\Admin\ReportManagementController::class, 'updateStatus'])->name('admin.reports.status');
    Route::get('/reports/analytics/overview', [App\Http\Controllers\Admin\ReportManagementController::class, 'analytics'])->name('admin.reports.analytics');
    
    // Admin message report management routes
    Route::get('/message-reports', [App\Http\Controllers\Admin\MessageReportManagementController::class, 'index'])->name('admin.message-reports.index');
    Route::get('/message-reports/{messageReport}', [App\Http\Controllers\Admin\MessageReportManagementController::class, 'show'])->name('admin.message-reports.show');
    Route::post('/message-reports/{messageReport}/comment', [App\Http\Controllers\Admin\MessageReportManagementController::class, 'addComment'])->name('admin.message-reports.comment');
    Route::patch('/message-reports/{messageReport}/status', [App\Http\Controllers\Admin\MessageReportManagementController::class, 'updateStatus'])->name('admin.message-reports.status');
    Route::post('/message-reports/{messageReport}/resolve', [App\Http\Controllers\Admin\MessageReportManagementController::class, 'resolve'])->name('admin.message-reports.resolve');
    Route::get('/message-reports/analytics/overview', [App\Http\Controllers\Admin\MessageReportManagementController::class, 'analytics'])->name('admin.message-reports.analytics');
    
    // Email management routes
    Route::resource('email/templates', App\Http\Controllers\Admin\EmailTemplateController::class);
    Route::resource('email/campaigns', App\Http\Controllers\Admin\EmailCampaignController::class);
    Route::post('email/campaigns/{campaign}/send', [App\Http\Controllers\Admin\EmailCampaignController::class, 'send'])
        ->name('admin.email.campaigns.send');
    
    // Admin Management Routes
    Route::get('/admins', [App\Http\Controllers\Admin\AdminManagementController::class, 'index'])->name('admin.admins.index');
    Route::get('/admins/create', [App\Http\Controllers\Admin\AdminManagementController::class, 'create'])->name('admin.admins.create');
    Route::post('/admins', [App\Http\Controllers\Admin\AdminManagementController::class, 'store'])->name('admin.admins.store');
    Route::get('/admins/{admin}', [App\Http\Controllers\Admin\AdminManagementController::class, 'show'])->name('admin.admins.show');
    Route::get('/admins/{admin}/edit', [App\Http\Controllers\Admin\AdminManagementController::class, 'edit'])->name('admin.admins.edit');
    Route::patch('/admins/{admin}', [App\Http\Controllers\Admin\AdminManagementController::class, 'update'])->name('admin.admins.update');
    Route::post('/admins/{user}/assign-role', [App\Http\Controllers\Admin\AdminManagementController::class, 'assignRole'])->name('admin.admins.assign-role');
    Route::delete('/admins/{user}/roles/{role}', [App\Http\Controllers\Admin\AdminManagementController::class, 'removeRole'])->name('admin.admins.remove-role');
    Route::get('/admins/workload', [App\Http\Controllers\Admin\AdminManagementController::class, 'workload'])->name('admin.admins.workload');
    
    // Fraud Detection Routes
    Route::get('/fraud-detection', [App\Http\Controllers\Admin\FraudDetectionController::class, 'index'])->name('admin.fraud-detection.index');
    Route::get('/fraud-detection/{id}', [App\Http\Controllers\Admin\FraudDetectionController::class, 'show'])->name('admin.fraud-detection.show');
    Route::post('/fraud-detection/{id}/review', [App\Http\Controllers\Admin\FraudDetectionController::class, 'review'])->name('admin.fraud-detection.review');
    Route::post('/fraud-detection/{id}/recalculate', [App\Http\Controllers\Admin\FraudDetectionController::class, 'recalculate'])->name('admin.fraud-detection.recalculate');
    Route::post('/fraud-detection/run-users', [App\Http\Controllers\Admin\FraudDetectionController::class, 'runDetectionUsers'])->name('admin.fraud-detection.run-users');
    Route::post('/fraud-detection/run-properties', [App\Http\Controllers\Admin\FraudDetectionController::class, 'runDetectionProperties'])->name('admin.fraud-detection.run-properties');
    Route::get('/fraud-detection/export', [App\Http\Controllers\Admin\FraudDetectionController::class, 'export'])->name('admin.fraud-detection.export');
    Route::get('/admins/{admin}/performance', [App\Http\Controllers\Admin\AdminManagementController::class, 'performance'])->name('admin.admins.performance');
    
    // Ticket Assignment Routes
    Route::post('/reports/{report}/assign', [App\Http\Controllers\Admin\TicketAssignmentController::class, 'assignReport'])->name('admin.reports.assign');
    Route::post('/reports/{report}/auto-assign', [App\Http\Controllers\Admin\TicketAssignmentController::class, 'autoAssignReport'])->name('admin.reports.auto-assign');
    Route::post('/message-reports/{messageReport}/assign', [App\Http\Controllers\Admin\TicketAssignmentController::class, 'assignMessageReport'])->name('admin.message-reports.assign');
    Route::post('/assignments/{assignment}/reassign', [App\Http\Controllers\Admin\TicketAssignmentController::class, 'reassign'])->name('admin.assignments.reassign');
    Route::post('/assignments/{assignment}/complete', [App\Http\Controllers\Admin\TicketAssignmentController::class, 'complete'])->name('admin.assignments.complete');
    Route::get('/assignments/workload', [App\Http\Controllers\Admin\TicketAssignmentController::class, 'workloadDistribution'])->name('admin.assignments.workload');
    Route::get('/assignments/statistics', [App\Http\Controllers\Admin\TicketAssignmentController::class, 'statistics'])->name('admin.assignments.statistics');
    
    // Analytics Routes
    Route::get('/analytics', [App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('admin.analytics.index');
    Route::get('/analytics/dashboard', [App\Http\Controllers\Admin\ReportAnalyticsController::class, 'dashboard'])->name('admin.analytics.dashboard');
    Route::get('/analytics/overview', [App\Http\Controllers\Admin\ReportAnalyticsController::class, 'overview'])->name('admin.analytics.overview');
    Route::get('/analytics/reports', [App\Http\Controllers\Admin\ReportAnalyticsController::class, 'reports'])->name('admin.analytics.reports');
    Route::get('/analytics/admins', [App\Http\Controllers\Admin\ReportAnalyticsController::class, 'admins'])->name('admin.analytics.admins');
    Route::get('/analytics/export', [App\Http\Controllers\Admin\ReportAnalyticsController::class, 'export'])->name('admin.analytics.export');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Enhanced profile routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Profile management routes
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar'])->name('profile.avatar');
    Route::get('/profile/settings', [ProfileController::class, 'settings'])->name('profile.settings');
    Route::patch('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::patch('/profile/preferences', [ProfileController::class, 'updatePreferences'])->name('profile.preferences');
    Route::get('/profile/statistics', [ProfileController::class, 'statistics'])->name('profile.statistics');
    
    // Role-specific profile routes
    Route::middleware(['role:renter'])->group(function () {
        Route::get('/profile/favorites', [ProfileController::class, 'favorites'])->name('profile.favorites');
        Route::get('/profile/reviews', [ProfileController::class, 'reviews'])->name('profile.reviews');
        Route::get('/profile/messages', [ProfileController::class, 'messages'])->name('profile.messages');
    });
    
    Route::middleware(['role:landlord'])->group(function () {
        Route::get('/profile/properties', [ProfileController::class, 'properties'])->name('profile.properties');
        Route::get('/profile/tenants', [ProfileController::class, 'tenants'])->name('profile.tenants');
        Route::get('/profile/analytics', [ProfileController::class, 'analytics'])->name('profile.analytics');
    });
    
    Route::middleware(['role:admin'])->group(function () {
        Route::get('/profile/permissions', [ProfileController::class, 'permissions'])->name('profile.permissions');
        Route::get('/profile/activity', [ProfileController::class, 'activity'])->name('profile.activity');
        Route::get('/profile/system', [ProfileController::class, 'system'])->name('profile.system');
    });
});

// Property Comparison Routes
Route::prefix('compare')->name('compare.')->group(function () {
    Route::get('/', [App\Http\Controllers\ComparisonController::class, 'index'])->name('index');
    Route::post('/add', [App\Http\Controllers\ComparisonController::class, 'add'])->name('add');
    Route::delete('/remove/{id}', [App\Http\Controllers\ComparisonController::class, 'remove'])->name('remove');
    Route::post('/clear', [App\Http\Controllers\ComparisonController::class, 'clear'])->name('clear');
    Route::get('/count', [App\Http\Controllers\ComparisonController::class, 'count'])->name('count');
    Route::post('/track-completion', [App\Http\Controllers\ComparisonController::class, 'trackCompletion'])->name('track-completion');
    Route::get('/analytics', [App\Http\Controllers\ComparisonController::class, 'analytics'])->name('analytics');
});

// API Routes for Maps Integration
Route::prefix('api')->group(function () {
    Route::get('/properties/geojson', [App\Http\Controllers\Api\PropertyController::class, 'geojson'])->name('api.properties.geojson');
    Route::get('/properties/search/radius', [App\Http\Controllers\Api\PropertyController::class, 'searchByRadius'])->name('api.properties.search.radius');
    Route::get('/properties/search/area', [App\Http\Controllers\Api\PropertyController::class, 'searchByArea'])->name('api.properties.search.area');
    Route::get('/properties/{property}/nearby', [App\Http\Controllers\Api\PropertyController::class, 'nearby'])->name('api.properties.nearby');
    Route::get('/properties/geocode', [App\Http\Controllers\Api\PropertyController::class, 'geocode'])->name('api.properties.geocode');
    Route::get('/properties/reverse-geocode', [App\Http\Controllers\Api\PropertyController::class, 'reverseGeocode'])->name('api.properties.reverse-geocode');
    Route::get('/properties/statistics', [App\Http\Controllers\Api\PropertyController::class, 'statistics'])->name('api.properties.statistics');
    Route::get('/properties/clusters', [App\Http\Controllers\Api\PropertyController::class, 'clusters'])->name('api.properties.clusters');
    
    // Advanced Search API Routes
    Route::get('/search/advanced', [App\Http\Controllers\AdvancedSearchController::class, 'search'])->name('api.search.advanced');
    Route::get('/search/suggestions', [App\Http\Controllers\AdvancedSearchController::class, 'suggestions'])->name('api.search.suggestions');
    Route::get('/search/filters', [App\Http\Controllers\AdvancedSearchController::class, 'filters'])->name('api.search.filters');
    Route::get('/search/analytics', [App\Http\Controllers\AdvancedSearchController::class, 'analytics'])->name('api.search.analytics');
    Route::get('/search/recommendations', [App\Http\Controllers\AdvancedSearchController::class, 'recommendations'])->name('api.search.recommendations');
    Route::get('/search/trends', [App\Http\Controllers\AdvancedSearchController::class, 'trends'])->name('api.search.trends');
    Route::post('/search/save', [App\Http\Controllers\AdvancedSearchController::class, 'saveSearch'])->name('api.search.save');
    Route::get('/search/saved', [App\Http\Controllers\AdvancedSearchController::class, 'savedSearches'])->name('api.search.saved');
    Route::delete('/search/saved/{id}', [App\Http\Controllers\AdvancedSearchController::class, 'deleteSavedSearch'])->name('api.search.delete');
    Route::post('/search/cache/clear', [App\Http\Controllers\AdvancedSearchController::class, 'clearCache'])->name('api.search.cache.clear');
});

require __DIR__.'/auth.php';