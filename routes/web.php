<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProductController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Frontend\AccountController;
use App\Http\Controllers\Frontend\WishlistController;
use App\Http\Controllers\Frontend\ReviewController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductManageController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ReviewManageController;
use App\Http\Controllers\Payment\StripeController;
use App\Http\Controllers\Payment\SslcommerzController;
use Illuminate\Support\Facades\Artisan;
use App\Models\User;

Route::get('/make-super-admin', function () {
    try {
        // তোমার কাঙ্ক্ষিত ইমেইলটি এখানে বসাও
        $email = 's0735949@gmail.com';

        $user = User::where('email', $email)->first();

        if (!$user) {
            return "User not found with email: " . $email;
        }

        // তোমার প্রজেক্টের লজিক অনুযায়ী নিচের যেকোনো একটি লাইন আনকমেন্ট (Uncomment) করো:

        // অপশন এ: যদি Spatie Permission প্যাকেজ ব্যবহার করো
        // $user->assignRole('super-admin'); // অথবা 'admin'

        // অপশন বি: যদি কলাম থাকে (যেমন: is_admin বা role)
        // $user->is_admin = 1; // অথবা $user->role = 'admin';
        // $user->save();

        return "User " . $email . " is now a Super Admin!";
    } catch (\Exception $e) {
        return "Error: " . $e->getMessage();
    }
});
Route::get('/run-migrate', function () {
    try {
        Artisan::call('migrate --force');
        return "Migration successful!<br><pre>" . Artisan::output() . "</pre>";
    } catch (\Exception $e) {
        return "Migration failed: " . $e->getMessage();
    }
});

/*
|--------------------------------------------------------------------------
| Frontend Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{product:slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/category/{category:slug}', [ProductController::class, 'byCategory'])->name('products.category');
Route::get('/search', [ProductController::class, 'search'])->name('products.search');

// Cart
Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/', [CartController::class, 'index'])->name('index');
    Route::post('/add', [CartController::class, 'add'])->name('add');
    Route::put('/update/{id}', [CartController::class, 'update'])->name('update');
    Route::delete('/remove/{id}', [CartController::class, 'remove'])->name('remove');
    Route::post('/apply-coupon', [CartController::class, 'applyCoupon'])->name('apply.coupon');
    Route::delete('/remove-coupon', [CartController::class, 'removeCoupon'])->name('remove.coupon');
});

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // Checkout
    Route::prefix('checkout')->name('checkout.')->group(function () {
        Route::get('/', [CheckoutController::class, 'index'])->name('index');
        Route::post('/place-order', [CheckoutController::class, 'placeOrder'])->name('place.order');
        Route::get('/success/{order}', [CheckoutController::class, 'success'])->name('success');
        Route::get('/cancel', [CheckoutController::class, 'cancel'])->name('cancel');
    });

    // Account
    Route::prefix('account')->name('account.')->group(function () {
        Route::get('/dashboard', [AccountController::class, 'dashboard'])->name('dashboard');
        Route::get('/orders', [AccountController::class, 'orders'])->name('orders');
        Route::get('/orders/{order}', [AccountController::class, 'orderDetail'])->name('orders.show');
        Route::get('/profile', [AccountController::class, 'profile'])->name('profile');
        Route::put('/profile', [AccountController::class, 'updateProfile'])->name('profile.update');
        Route::put('/change-password', [AccountController::class, 'changePassword'])->name('password.change');
        Route::get('/addresses', [AccountController::class, 'addresses'])->name('addresses');
        Route::post('/addresses', [AccountController::class, 'storeAddress'])->name('addresses.store');
        Route::put('/addresses/{address}', [AccountController::class, 'updateAddress'])->name('addresses.update');
        Route::delete('/addresses/{address}', [AccountController::class, 'deleteAddress'])->name('addresses.delete');
    });

    // Wishlist
    Route::prefix('wishlist')->name('wishlist.')->group(function () {
        Route::get('/', [WishlistController::class, 'index'])->name('index');
        Route::post('/add/{product}', [WishlistController::class, 'add'])->name('add');
        Route::delete('/remove/{product}', [WishlistController::class, 'remove'])->name('remove');
    });

    // Reviews
    Route::post('/products/{product}/review', [ReviewController::class, 'store'])->name('review.store');
});

/*
|--------------------------------------------------------------------------
| Payment Routes
|--------------------------------------------------------------------------
*/

// ── Stripe ──
// GET: Initiated from CheckoutController redirect after order is saved
Route::get('/payment/stripe/init', [StripeController::class, 'init'])
    ->middleware('auth')
    ->name('stripe.init');

// GET: Stripe redirects here after payment
Route::get('/payment/stripe/success', [StripeController::class, 'success'])
    ->name('stripe.success');

// GET: Stripe redirects here on cancel
Route::get('/payment/stripe/cancel', [StripeController::class, 'cancel'])
    ->name('stripe.cancel');

// POST: Stripe webhook (server-to-server, no auth/csrf)
Route::post('/payment/stripe/webhook', [StripeController::class, 'webhook'])
    ->name('stripe.webhook')
    ;

// ── SSLCommerz ──
// GET: Initiated from CheckoutController redirect after order is saved
Route::get('/payment/sslcommerz/init', [SslcommerzController::class, 'initGet'])
    ->middleware('auth')
    ->name('sslcommerz.init.get');

// POST: SSLCommerz calls these (no auth needed, server-to-server)
Route::post('/payment/sslcommerz/success', [SslcommerzController::class, 'success'])
    ->name('sslcommerz.success')
    ;

Route::post('/payment/sslcommerz/fail', [SslcommerzController::class, 'fail'])
    ->name('sslcommerz.fail')
    ;

Route::post('/payment/sslcommerz/cancel', [SslcommerzController::class, 'cancel'])
    ->name('sslcommerz.cancel')
    ;

Route::post('/payment/sslcommerz/ipn', [SslcommerzController::class, 'ipn'])
    ->name('sslcommerz.ipn')
    ;
/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:super-admin|admin|manager|staff'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Products - admin|manager can manage
    Route::middleware('role:super-admin|admin|manager')->group(function () {
        Route::resource('products', ProductManageController::class);
        Route::post('/products/{product}/toggle-featured', [ProductManageController::class, 'toggleFeatured'])->name('products.toggle.featured');
        Route::post('/products/{product}/images', [ProductManageController::class, 'uploadImages'])->name('products.images.upload');
        Route::delete('/products/images/{image}', [ProductManageController::class, 'deleteImage'])->name('products.images.delete');

        Route::resource('categories', CategoryController::class);

        Route::resource('coupons', CouponController::class);
        Route::post('/coupons/{coupon}/toggle', [CouponController::class, 'toggle'])->name('coupons.toggle');

        Route::resource('banners', BannerController::class);
    });

    // Orders - all admin roles can view
    Route::resource('orders', OrderController::class)->only(['index', 'show', 'update']);
    Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.status');
    Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');

    // Users - only super-admin and admin
    Route::middleware('role:super-admin|admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::post('/users/{user}/toggle', [UserController::class, 'toggle'])->name('users.toggle');
    });

    // Roles & Permissions - only super-admin
    Route::middleware('role:super-admin')->group(function () {
        Route::resource('roles', RoleController::class);
        Route::post('/roles/{role}/permissions', [RoleController::class, 'syncPermissions'])->name('roles.permissions');
    });

    // Reviews
    Route::resource('reviews', ReviewManageController::class)->only(['index', 'show', 'destroy']);
    Route::post('/reviews/{review}/approve', [ReviewManageController::class, 'approve'])->name('reviews.approve');

    // Reports - admin and above
    Route::middleware('role:super-admin|admin')->prefix('reports')->name('reports.')->group(function () {
        Route::get('/sales', [ReportController::class, 'sales'])->name('sales');
        Route::get('/products', [ReportController::class, 'products'])->name('products');
        Route::get('/customers', [ReportController::class, 'customers'])->name('customers');
        Route::get('/export/{type}', [ReportController::class, 'export'])->name('export');
    });

    // Settings - only super-admin
    Route::middleware('role:super-admin')->prefix('settings')->name('settings.')->group(function () {
        Route::get('/general', [SettingController::class, 'general'])->name('general');
        Route::post('/general', [SettingController::class, 'updateGeneral'])->name('general.update');
        Route::get('/payment', [SettingController::class, 'payment'])->name('payment');
        Route::post('/payment', [SettingController::class, 'updatePayment'])->name('payment.update');
        Route::get('/email', [SettingController::class, 'email'])->name('email');
        Route::post('/email', [SettingController::class, 'updateEmail'])->name('email.update');
        Route::get('/shipping', [SettingController::class, 'shipping'])->name('shipping');
        Route::post('/shipping', [SettingController::class, 'updateShipping'])->name('shipping.update');
    });
});

// Admin live search
Route::get('/admin/search', [\App\Http\Controllers\Admin\DashboardController::class, 'search'])
    ->middleware(['auth', 'role:super-admin|admin|manager|staff'])
    ->name('admin.search');

// Frontend live search suggest
Route::get('/search/suggest', [\App\Http\Controllers\Frontend\ProductController::class, 'suggest'])->name('products.suggest');
