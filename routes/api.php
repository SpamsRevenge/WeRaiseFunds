<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\LogoutController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\PageController;
use App\Http\Controllers\Api\FundraiserController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\ContactUsController;
use App\Http\Controllers\Api\GeneralController;
use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\Api\RaffleController;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

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

Route::middleware(['auth:sanctum','api'])->group(function(){
	// User profile and logout
	Route::post('/logout', LogoutController::class);
	Route::get('/profile', [UserController::class, 'profile'] );

	// Update User Profile
	Route::post('/profile', [ UserController::class, 'update' ]);

	// Get all orders related to user
	Route::get('/get_orders', [ OrderController::class, 'index' ]);

	Route::get('/get_order/{id}', [ OrderController::class, 'show' ]);

	// Fundraiser and Categories fetch by auth user
	Route::get('/fundraiser_sellers/', [FundraiserController::class, 'fundraiserSellers'] );
	Route::get('/fundraiser_by_user', [FundraiserController::class, 'fundraiserByUser'] );
	Route::get('/fundraiser_categories_by_user', [FundraiserController::class, 'fundraiserCategoriesByUser'] );

	Route::get('/fundraiser_categories_by_user', [FundraiserController::class, 'fundraiserCategoriesByUser'] );

	Route::get('/school_fundraiser_managers', [FundraiserController::class, 'schoolFundraiserManagers'] );
	
	Route::post('/send_fundraiser_notification', [FundraiserController::class, 'sendFundraiserNotification'] );

	// Fundraiser Update
	Route::post('fundraiser_update/{slug}', [FundraiserController::class, 'update']);
	
	// Sellers Fundraiser Update
	Route::post('/seller_fundraiser_update', [FundraiserController::class, 'updateSellerFundraiser']);

	Route::get('/seller_fundraiser_get', [FundraiserController::class, 'getSellerFundraiserAll']);

	// Fetch all users for testing perpose only
	Route::get('/all_users', [UserController::class, 'index'] );

	// Refund order api
	Route::post('/refund_initiate', [OrderController::class, 'refundOrder'])->name('refund_initiate');
	Route::get('/refund_initiate_nova', [OrderController::class, 'refundOrderNova'])->name('refund_initiate_nova');


	// Raffle apis define
	Route::get('/get_raffles', [RaffleController::class, 'index']);
	Route::post('/create_raffle', [RaffleController::class, 'store']);
	
});


Route::middleware('api')->group(function(){

	Route::get('/get_orders_email/{id}', [ OrderController::class, 'orderemail' ]);

	// User Login and Register define
	Route::post('/login', LoginController::class)->middleware('guest');
	Route::post('/register', RegisterController::class);
	// check email existing
	Route::get('/check_email_exists', [RegisterController::class, 'checkEmailExists'] );

	// send reset password email
	Route::post('/send_reset_password_link', [ UserController::class, 'sendResetPasswordEmail' ] );
	// save updated password
	Route::post('/password_update', [ UserController::class, 'passwordUpdate' ] );

	// Fundraiser CRUD define
	Route::resource('/fundraisers', FundraiserController::class);

	// Fundraiser Categories fetch
	Route::get('/fundraiser_categories', [FundraiserController::class, 'categories'] );
	Route::get('/fundraiser_category/{slug}', [FundraiserController::class, 'categoryBySlug'] );


	// Blog CRUD define
	Route::resource('blogs', BlogController::class);

	// Blog Categories fetch
	Route::get('/blog_categories', [BlogController::class, 'blogCategories'] );
	Route::get('/blog_category/{slug}', [BlogController::class, 'blogCategoryBySlug'] );

	// Page fetch by slug
	Route::get('/page/{slug}', [PageController::class, 'show'] );

	// Fetch All Menus
	Route::get('/get_menus', [PageController::class, 'getMenus'] );

	Route::get('/get_menus/{slug?}', [PageController::class, 'getMenus'] );


	// Sellers Fundraiser Get
	Route::get('/seller_fundraiser_update', [FundraiserController::class, 'getSellerFundraiser']);

	// Contact Form Submission
	Route::post('/contact_us', [ContactUsController::class, 'store']);

	// Site Settings Fetch
	Route::resource('/get_settings', GeneralController::class);

	// Order Create api
	Route::post('/make_donation', [OrderController::class, 'store']);
});


