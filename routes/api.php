<?php

use App\Http\Controllers\Api\V1\ProductCategory\AdminProductCategoryController;
use App\Http\Controllers\Api\V1\ArticleCategory\AdminArticleCategoryController;
use App\Http\Controllers\Api\V1\Contact\AdminContactController;
use App\Http\Controllers\Api\V1\Product\AdminProductController;
use App\Http\Controllers\Api\V1\Product\AdminProductImageController;
use App\Http\Controllers\Api\V1\Admin\AdminProfileController;
use App\Http\Controllers\Api\V1\ArticleAuthor\AdminArticleAuthorController;
use App\Http\Controllers\Api\V1\Article\AdminArticleController;
use App\Http\Controllers\Api\V1\Subscriber\AdminSubscriberController;
use App\Http\Controllers\Api\V1\Event\AdminEventController;

use App\Http\Controllers\Api\V1\User\Auth\AuthUserController;
use App\Http\Controllers\Api\V1\User\ForgetPasswordController;
use App\Http\Controllers\Api\V1\User\UserProfileController;
use App\Http\Controllers\Api\V1\Wishlist\UserWishlistController;

use App\Http\Controllers\Api\V1\ProductCategory\ProductCategoryController;
use App\Http\Controllers\Api\V1\Contact\ContactController;
use App\Http\Controllers\Api\V1\Product\ProductController;
use App\Http\Controllers\Api\V1\Article\ArticleController;
use App\Http\Controllers\Api\V1\Event\EventController;
use App\Http\Controllers\Api\V1\Subscriber\SubscriberController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\V1\Admin\AdminController;
use App\Http\Controllers\Api\V1\Admin\AuthAdminController;
use App\Http\Controllers\Api\V1\Admin\AdminEmailChangesVerificationController;
use App\Http\Controllers\Api\V1\Admin\AdminFeatureController;
use App\Http\Controllers\Api\V1\Admin\ForgetAdminPasswordController;
use App\Http\Controllers\Api\V1\Article\AdminArticleImagesController;
use App\Http\Controllers\Api\V1\Cart\UserCartController;
use App\Http\Controllers\Api\V1\Event\AdminEventImagesController;
use App\Http\Controllers\Api\V1\User\AdminUserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('/v1')->middleware('verifyLanguage')->group(function () {

    //======= PUBLIC =========

    //Contacts
    Route::post('/contacts', [ContactController::class,'store']);

    //Subscriber
    Route::post('/subscribers', [SubscriberController::class, 'store']);

    //ProductCategories
    Route::get('/product-categories', [ProductCategoryController::class, 'index']);

    //Article
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/articles/{article:slug}', [ArticleController::class, 'show']);
    Route::get('/top-articles', [ArticleController::class, 'top']);

    //Event
    Route::get('/events', [EventController::class, 'index']);
    Route::get('/events/{article:slug}', [EventController::class, 'show']);
    Route::get('/top-events', [EventController::class, 'topEvents']);

    //Products
    Route::get('/products/{slug}', [ProductController::class,'show']);
    Route::get('/product-categories/{category:slug}', [ProductController::class,'index']);
    Route::get('/more-products/{category:slug}', [ProductController::class, 'moreProducts']);

    //======= END PUBLIC ROUTE ==========

    Route::prefix('/admins')->group(function () {

        // Admin - Login
        Route::post('/login', [AuthAdminController::class, 'login']);
        Route::post('/resend-email-verification', [AuthAdminController::class, 'resendEmailVerification'])->middleware('throttle:limit_public_api_with_email_sending');
        Route::post('/email-verification/{token}', [AuthAdminController::class, 'emailVerification']);

        // Admin - Forget Password
        Route::post('/forget-password', [ForgetAdminPasswordController::class, 'forgetPassword'])->middleware('throttle:limit_public_api_with_email_sending');
        Route::post('/forget-password/verify-pin', [ForgetAdminPasswordController::class, 'verifyPin']);
        Route::post('/forget-password/reset-password', [ForgetAdminPasswordController::class, 'resetPassword']);
        Route::post('/verify-email-changes/{token}', AdminEmailChangesVerificationController::class);

        Route::group(['middleware' => ['auth:api_admin', 'scopes:admin', 'verifyUserActive']], function(){

            // Admin - Logout
            Route::post('/logout', [AuthAdminController::class, 'logout']);

            // Admin - Profile
            Route::get('/profile', [AdminProfileController::class, 'index']);
            Route::put('/profile', [AdminProfileController::class, 'update']);
            Route::post('/change-password', [AdminProfileController::class, 'changePassword']);

            // Admin - Contacts
            Route::get('/contacts', [AdminContactController::class,'index']);
            Route::get('/contacts/{contact}', [AdminContactController::class,'show']);

            // Admin - Subscriber
            Route::get('/subscribers', [AdminSubscriberController::class, 'index']);
            Route::delete('/subscribers/{subscriber:id}', [AdminSubscriberController::class, 'destroy']);

            // Admin - ArticleAuthor
            Route::get('/authors', [AdminArticleAuthorController::class, 'index']);
            Route::get('/authors/{author}', [AdminArticleAuthorController::class, 'show']);
            Route::post('/authors', [AdminArticleAuthorController::class, 'store']);
            Route::put('/authors/{author}', [AdminArticleAuthorController::class, 'update']);
            Route::delete('/authors/{author}', [AdminArticleAuthorController::class, 'destroy']);

            // Admin - Article
            Route::get('/articles', [AdminArticleController::class, 'index']);
            Route::post('/articles', [AdminArticleController::class, 'store']);
            Route::get('/articles/{article:slug}', [AdminArticleController::class, 'show']);
            Route::put('/articles/{article:slug}', [AdminArticleController::class, 'update']);
            Route::delete('/articles/{article:slug}', [AdminArticleController::class, 'destroy']);
            Route::post('/article-images', [AdminArticleImagesController::class, 'store']);

            // Admin - Event
            Route::get('/events', [AdminEventController::class, 'index']);
            Route::post('/events', [AdminEventController::class, 'store']);
            Route::get('/events/{event:slug}', [AdminEventController::class, 'show']);
            Route::put('/events/{event:slug}', [AdminEventController::class, 'update']);
            Route::delete('/events/{event:slug}', [AdminEventController::class, 'destroy']);

            // Admin - EventImage
            Route::get('/events/{event:slug}/images', [AdminEventImagesController::class, 'index']);
            Route::post('/events/{event:slug}/images', [AdminEventImagesController::class,'store']);
            Route::post('/events/{event:slug}/save-images', [AdminEventImagesController::class, 'saveImages']);

            // Admin - ProductCategory
            Route::get('/product-categories', [AdminProductCategoryController::class, 'index']);
            Route::post('/product-categories', [AdminProductCategoryController::class, 'store']);
            Route::get('/product-categories/{category:slug}', [AdminProductCategoryController::class,'show']);
            Route::put('/product-categories/{category:slug}', [AdminProductCategoryController::class, 'update']);
            Route::delete('/product-categories/{category:slug}', [AdminProductCategoryController::class, 'destroy']);

            //ArticleCategory
            Route::get('/article-categories', [AdminArticleCategoryController::class, 'index']);
            Route::post('/article-categories', [AdminArticleCategoryController::class, 'store']);
            Route::get('/article-categories/{category:slug}', [AdminArticleCategoryController::class,'show']);
            Route::put('/article-categories/{category:slug}', [AdminArticleCategoryController::class, 'update']);
            Route::delete('/article-categories/{category:slug}', [AdminArticleCategoryController::class, 'destroy']);

            // Admin - Products
            Route::get('/products', [AdminProductController::class,'index']);
            Route::post('/products', [AdminProductController::class, 'store']);
            Route::get('/products/{product:slug}', [AdminProductController::class,'show']);
            Route::put('/products/{product:slug}', [AdminProductController::class,'update']);
            Route::delete('/products/{product}', [AdminProductController::class,'destroy']);

            // Admin - ProductImage
            Route::get('/products/{product:slug}/images', [AdminProductImageController::class, 'index']);
            Route::post('/products/{product:slug}/images', [AdminProductImageController::class,'store']);
            Route::post('/products/{product:slug}/save-images', [AdminProductImageController::class, 'saveImages']);

            // Admin - User
            Route::get('/users', [AdminUserController::class, 'index']);
            Route::put('/users/{user:uuid}', [AdminUserController::class, 'update']);

            // Admin - Feature
            Route::get('/admin-features', [AdminFeatureController::class, 'index']);

           // Admin - Admin
           Route::get('/', [AdminController::class, 'index']);
           Route::get('/{admin}', [AdminController::class, 'show']);
           Route::post('', [AdminController::class, 'store']);
           Route::put('/{admin}', [AdminController::class, 'update']);
           Route::delete('/{admin}', [AdminController::class, 'destroy']);
        });
    });

    //User routes
    Route::prefix('/users')->group(function(){

        //Authentication
        Route::post('/register', [AuthUserController::class,'register']);
        Route::post('/resend-email-verification', [AuthUserController::class, 'resendEmailVerification'])->middleware('throttle:limit_public_api_with_email_sending');
        Route::post('/email-verification/{token}', [AuthUserController::class, 'emailVerification']);
        Route::post('/login', [AuthUserController::class, 'login']);

        //password controller
        Route::post('/password/forget', [ForgetPasswordController::class, 'forgetPassword']);
        Route::post('/password/forget/verify-otp', [ForgetPasswordController::class, 'verifyPin']);
        Route::post('/password/reset/', [ForgetPasswordController::class, 'resetPassword']);
        Route::get('/password/forget/resend', [ForgetPasswordController::class,'forgetPassword']);

        Route::middleware(['auth:api_user'])->group(function(){
            //User Profile
            Route::get('/profile', [UserProfileController::class, 'index']);
            Route::put('/profile', [UserProfileController::class, 'update']);
            Route::post('/change-password', [UserProfileController::class, 'changePassword']);
            Route::post('/change-language', [UserProfileController::class, 'changeLanguage']);

             //Authentication
             Route::post('/logout', [AuthUserController::class, 'logout']);

             //password controller
             Route::post('/change-password', [UserProfileController::class, 'changePassword']);

             //Wishlists
             Route::get('/wishlists-side', [UserWishlistController::class, 'listSide']);
             Route::get('/wishlists', [UserWishlistController::class, 'index']);
             Route::post('/wishlists', [UserWishlistController::class,'store']);
             Route::delete('/wishlists', [UserWishlistController::class, 'destroy']);

            // Cart
            Route::get('/carts-side', [UserCartController::class, 'listSide']);
            Route::get('/carts', [UserCartController::class, 'index']);
            Route::post('/carts', [UserCartController::class,'store']);
            Route::delete('/carts', [UserCartController::class, 'destroy']);

            // Promo
            // Route::post('/apply-promo', UserPromoController::class);
        });
    });
});

