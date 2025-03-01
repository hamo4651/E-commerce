# Laravel E-Commerce API

Welcome to the **Laravel E-Commerce API**, a robust and secure backend for managing user authentication, product listings, cart functionality, orders, and payments. This API is designed to support e-commerce applications, providing seamless user experiences with authentication, cart management, order processing, and payment integration.

## Features
- **User Authentication & Authorization** (Registration, Login, Logout, Profile)
- **Product & Category Management** (Admin-restricted CRUD operations)
- **Cart System** (Add, Update, Remove, and Clear Cart Items)
- **Order Management** (Create, View, and Update Order Status)
- **Payment Integration** (Stripe & PayPal support)
- **Social Authentication** (Google Login)
- **Email Verification** (Secure user validation)

---

## API Routes

### **Authentication Routes**
```php
Route::post('/register', [AuthController::class, 'register'])->name('register');
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::group(["middleware" => "auth:sanctum"], function () {
    Route::get('/user', [AuthController::class, 'profile']);
    Route::post('/logout', [AuthController::class, 'logout']);
});
```
- **Register**: Create a new user account.
- **Login**: Authenticate a user and generate an access token.
- **Profile**: Fetch authenticated user details.
- **Logout**: Invalidate the access token.

### **Category Routes**
```php
Route::apiResource('categories', CategoryController::class)
    ->only(['index', 'show'])
    ->middleware(['auth:sanctum', 'verified']);

Route::apiResource('categories', CategoryController::class)
    ->except(['index', 'show'])
    ->middleware(['auth:sanctum', 'verified', 'is_admin']);
```
- **Public Access**: View categories.
- **Admin Access**: Create, update, and delete categories.

### **Product Routes**
```php
Route::apiResource('products', ProductController::class)
    ->only(['index', 'show'])
    ->middleware(['auth:sanctum', 'verified']);

Route::apiResource('products', ProductController::class)
    ->except(['index', 'show'])
    ->middleware(['auth:sanctum', 'verified', 'is_admin']);
```
- **Public Access**: View products.
- **Admin Access**: Manage products.

### **Cart Routes**
```php
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::post('/cart', [CartController::class, 'addToCart']);
    Route::get('/cart', [CartController::class, 'getCartItems']);
    Route::put('/cart/{id}', [CartController::class, 'updateCartItem']);
    Route::delete('/cart/{id}', [CartController::class, 'removeCartItem']);
    Route::post('/cart/clear', [CartController::class, 'clearCart']);
});
```
- **Authenticated Users Only**: Manage shopping cart items.

### **Order Routes**
```php
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders', [OrderController::class, 'index']);
    Route::get('/orders/{id}', [OrderController::class, 'show']);
    Route::put('/orders/{id}/status', [OrderController::class, 'updateStatus']);
});
```
- **Authenticated Users Only**: Place and track orders.
- **Admins**: Update order status.

### **Payment Routes**
```php
Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::post('/stripe/payment', [PaymentController::class, 'stripePayment']);
    Route::post('/paypal/payment', [PaymentController::class, 'paypalPayment']);
    Route::post('/paypal/capture', [PaymentController::class, 'paypalCapture']);
});
```
- **Stripe & PayPal Integration**: Secure payment processing.

### **Google Authentication**
```php
Route::get('auth/google', [GoogleAuthController::class, 'redirectToGoogle'])->name('google.login');
Route::get('auth/google/callback', [GoogleAuthController::class, 'handleGoogleCallback']);
```
- **Google Login**: Authenticate users via Google OAuth.

### **Email Verification**
```php
Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['auth:sanctum', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth:sanctum', 'throttle:6,1'])
    ->name('verification.send');
```
- **User Verification**: Secure email validation.

---

## Installation & Setup

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-repository.git
   cd your-repository
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Setup environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Run migrations**
   ```bash
   php artisan migrate --seed
   ```

5. **Start the server**
   ```bash
   php artisan serve
   ```

6. **Generate API documentation (Optional)**
   ```bash
   php artisan scribe:generate
   ```

---

## Authentication

This API uses **Laravel Sanctum** for authentication. To access protected routes, include the **Bearer Token** in the `Authorization` header of your requests.

```http
Authorization: Bearer your-access-token
```

---

