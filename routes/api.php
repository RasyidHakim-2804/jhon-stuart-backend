<?php

use App\Http\Controllers\Api\V1\Admin\AdminAdminController;
use App\Http\Controllers\Api\V1\Admin\AdminUserController;
use App\Http\Controllers\Api\V1\Auth\Admin\AdminAuthController;
use App\Http\Controllers\Api\V1\Auth\Admin\AdminPasswordController;
use App\Http\Controllers\Api\V1\ProductCategory\AdminProductCategoryController;
use App\Http\Controllers\Api\V1\ArticleCategory\AdminArticleCategoryController;
use App\Http\Controllers\Api\V1\Contact\AdminContactController;
use App\Http\Controllers\Api\V1\Product\AdminProductController;
use App\Http\Controllers\Api\V1\Product\AdminProductImageController;
use App\Http\Controllers\Api\V1\Profile\AdminProfileController;
use App\Http\Controllers\Api\V1\ArticleAuthor\AdminArticleAuthorController;
use App\Http\Controllers\Api\V1\Article\AdminArticleController;
use App\Http\Controllers\Api\V1\Subscriber\AdminSubscriberController;

use App\Http\Controllers\Api\V1\Auth\User\UserAuthController;
use App\Http\Controllers\Api\V1\Auth\User\UserPasswordController;
use App\Http\Controllers\Api\V1\Profile\UserProfileController;
use App\Http\Controllers\Api\V1\Wishlist\UserWishlistController;

use App\Http\Controllers\Api\V1\ProductCategory\ProductCategoryController;
use App\Http\Controllers\Api\V1\Contact\ContactController;
use App\Http\Controllers\Api\V1\Product\ProductController;
use App\Http\Controllers\Api\V1\Article\ArticleController;
use App\Http\Controllers\Api\V1\Subscriber\SubscriberController;
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

Route::prefix('v1')->group(function(){
    //Admin routes
    Route::prefix('admins')->group(function (){

        //Authentication
        Route::post('/email-verify/confirm/{token}', [AdminAuthController::class, 'verifyEmail']);
        Route::post('/email-verify/resend', [AdminAuthController::class, 'resendEmailVerification']);
        Route::post('/sign-in', [AdminAuthController::class, 'login']);

        //password controller
        Route::post('/password/forget', [AdminPasswordController::class, 'forgetPassword']);
        Route::post('/password/forget/verify-otp', [AdminPasswordController::class, 'verifyOtp']);
        Route::post('/password/reset', [AdminPasswordController::class, 'resetPassword']);
        Route::get('/password/forget/resend', [AdminPasswordController::class, 'forgetPassword']);
        
        Route::middleware(['auth:sanctum', 'verified', 'abilities:is_admin'])->group(function(){
            
            //Authentication
            Route::post('/logout', [AdminAuthController::class, 'logout']);            
            
            //password controller
            Route::post('/password/change', [AdminPasswordController::class, 'changePassword']);
            
            //Profile
            Route::get('/profile', [AdminProfileController::class,'show']);
            Route::put('/profile', [AdminProfileController::class, 'update']);
            
            //Contacts
            Route::get('/contacts', [AdminContactController::class,'index']);
            Route::get('/contacts/{contact}', [AdminContactController::class,'show']);

            //Subscriber
            Route::get('/subscribers', [AdminSubscriberController::class, 'index']);

            //ArticleAuthor
            Route::get('/authors', [AdminArticleAuthorController::class, 'index']);
            Route::get('/authors/{author}', [AdminArticleAuthorController::class, 'show']);
            Route::post('/authors', [AdminArticleAuthorController::class, 'store']);
            Route::put('/authors/{author}', [AdminArticleAuthorController::class, 'update']);
            Route::delete('/authors/{author}', [AdminArticleAuthorController::class, 'destroy']);

            //Article
            Route::get('/articles', [AdminArticleController::class, 'index']);
            Route::post('/articles', [AdminArticleController::class, 'store']);
            Route::get('/articles/{article:slug}', [AdminArticleController::class, 'show']);
            Route::put('/articles/{article:slug}', [AdminArticleController::class, 'update']);
            Route::delete('/articles/{article:slug}', [AdminArticleController::class, 'destroy']);

            //ProductCategory
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
  
            //Products
            Route::get('/products', [AdminProductController::class,'index']);
            Route::post('/products', [AdminProductController::class, 'store']);
            Route::get('/products/{product}', [AdminProductController::class,'show']);
            Route::put('/products/{product}', [AdminProductController::class,'update']);
            Route::delete('/products/{product}', [AdminProductController::class,'destroy']);

            //User
            Route::get('/users', [AdminUserController::class, 'index']);
            Route::get('/users/{user}', [AdminUserController::class, 'show']);
            Route::put('/users/{user}', [AdminUserController::class, 'update']);
            
            //ProductImage
            Route::get('/products/{product}/images', [AdminProductImageController::class, 'index']);
            Route::post('/products/{product}/images', [AdminProductImageController::class,'store']);
            Route::post('/products/{product}/save-images', [AdminProductImageController::class, 'saveImages']);
            
            //Admin
            Route::post('/', [AdminAdminController::class, 'store'])->middleware('ability:admin_master');
            Route::get('/', [AdminAdminController::class,'index']);
            Route::put('/{admin}', [AdminAdminController::class, 'update'])->middleware('ability:admin_master');
            Route::get('/{admin}',[AdminAdminController::class, 'show']);
        });

        
    });
    
    //User routes
    Route::prefix('users')->group(function(){

        //Authentication
        Route::post('/register', [UserAuthController::class,'register']);
        Route::post('/email-verify/confirm/{token}', [UserAuthController::class, 'verifyEmail']);
        Route::post('/email-verify/resend', [UserAuthController::class, 'resendEmailVerification']);
        Route::post('/sign-in', [UserAuthController::class, 'login']);

        //password controller
        Route::post('/password/forget', [UserPasswordController::class, 'forgetPassword']);
        Route::post('/password/forget/verify-otp', [UserPasswordController::class, 'verifyOtp']);
        Route::post('/password/reset/', [UserPasswordController::class, 'resetPassword']);
        Route::get('/password/forget/resend', [UserPasswordController::class,'forgetPassword']);
    

        Route::middleware(['auth:sanctum','verified', 'ability:is_user'])->group(function(){

            //Authentication
            Route::post('/logout', [UserAuthController::class, 'logout']);

            //password controller
            Route::post('/password/change', [UserPasswordController::class, 'changePassword']);

            //User Profile
            Route::get('/profile', [UserProfileController::class,'show']);
            Route::put('/profile', [UserProfileController::class, 'update']);

            //Wishlists
            Route::get('/wishlists', [UserWishlistController::class, 'index'])->middleware('language');
            Route::post('/wishlists', [UserWishlistController::class,'store']);
            Route::delete('/wishlists', [UserWishlistController::class, 'destroy']);
            
        });
    });
    
    //Contacts
    Route::post('/contacts', [ContactController::class,'store']);

    //Subscriber
    Route::post('/subscribers', [SubscriberController::class, 'store']);

    //ProductCategories
    Route::get('/product-categories', [ProductCategoryController::class, 'index']);
    
    //Article
    Route::get('/articles', [ArticleController::class, 'index']);
    Route::get('/articles/{article:slug}', [ArticleController::class, 'show']);
    Route::get('/top-articles', [ArticleController::class, 'topArticles']);
    
    //Products
    Route::get('/products/{slug}', [ProductController::class,'show']);
    Route::get('/product-categories/{category:slug}', [ProductController::class,'index']);
    Route::get('/more-products/{category:slug}', [ProductController::class, 'moreProducts']);
});
