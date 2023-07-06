<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\Auth\Admin\RegisterController as AdminRegisterController;
use App\Http\Controllers\Api\Auth\Admin\LoginController as AdminLoginController;
use App\Http\Controllers\Api\Auth\Admin\ForgotPasswordController as AdminForgotPasswordController;
use App\Http\Controllers\Api\Auth\Admin\ResetPasswordController as AdminResetPasswordController;
use App\Http\Controllers\Api\Auth\Admin\MoodleLoginController as AdminMoodleLoginController;
// use App\Http\Controllers\Api\Auth\Admin\MoodleRegisterController as AdminMoodleRegisterController;

use App\Http\Controllers\Api\Auth\Customer\RegisterController;
use App\Http\Controllers\Api\Auth\Customer\LoginController;
use App\Http\Controllers\Api\Auth\Customer\ForgotPasswordController;
use App\Http\Controllers\Api\Auth\Customer\ResetPasswordController;
use App\Http\Controllers\Api\Auth\Customer\MoodleLoginController;
use App\Http\Controllers\Api\Auth\Customer\MoodleRegisterController;
use App\Http\Controllers\Api\Customer\PlanController;
use App\Http\Controllers\Api\Customer\SubscriptionController;
use App\Http\Controllers\Api\Customer\ProfileController;
use App\Http\Controllers\Api\Customer\SuppliersController;
use App\Http\Controllers\Api\Customer\GalleriesController;
// use App\Http\Controllers\Api\Customer\MoodBoardController;
use App\Http\Controllers\Api\Customer\MoodBoardImagesController;
use App\Http\Controllers\Api\Customer\MoodBoardBackgroundsController;
use App\Http\Controllers\Api\Customer\MoodBoardFramesController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MoodBoardController;
use App\Http\Controllers\MoodBoardItemController;
use App\Http\Controllers\StripeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\MoodBoardBackgroundController;
use App\Http\Controllers\MoodBoardFrameController;
use App\Http\Controllers\TemplateController;

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

// jwt auth
Route::group(["prefix" => "auth"], function () {
    Route::post("/register", [AuthController::class, "register"]);
    Route::post("/login", [AuthController::class, "login"]);
    Route::post("/admin-login", [AuthController::class, "admin_login"]);
    Route::post("/forgot-password", [AuthController::class, "forgot_password"]);
    Route::post('/validate-code', [AuthController::class, "validate_code"]);
    Route::post('/change-password', [AuthController::class, "change_password"]);

    Route::middleware("auth:sanctum")->group(function () {
    //     Route::post("/user", [UserController::class, "createUser"]);
        Route::put("/user/{id}", [UserController::class, "update"]);
    //     Route::delete("/user/{id}", [UserController::class, "deleteUser"]);
        Route::get("/logout", [AuthController::class, "logout"]);
        Route::get("/user", [UserController::class, "get_user"]);
        // Route::get("/users", [UserController::class, "list"]);
    //     Route::get("/user/permissions", [UserController::class, "getPermissions"]);
        Route::get("/test", [AuthController::class, "test"]);
    });
});

Route::group(["prefix"=>"admin"],function(){
    Route::middleware('auth:sanctum')->group(function(){
        Route::get('/templates',[TemplateController::class,'index']);
        Route::group(['prefix'=>'template'],function(){
            // Route::get("/", [TemplateController::class,'index']);
            Route::get("/{id}", [TemplateController::class,'get']);
            Route::put("/{id}", [TemplateController::class,'update']);
            Route::delete("/{id}", [TemplateController::class,'delete']);
            Route::post("/create", [TemplateController::class,'store']);
        });

        Route::get('/backgrounds',[MoodBoardBackgroundController::class,'index']);
        Route::group(['prefix'=>'background'],function(){
            Route::get('/{id}',[MoodBoardBackgroundController::class,'get']);
            Route::put('/{id}',[MoodBoardBackgroundController::class,'update']);
            Route::delete('/{id}',[MoodBoardBackgroundController::class,'delete']);
            Route::post('/create',[MoodBoardBackgroundController::class,'create']);
        });

        Route::get('/frames',[MoodBoardFrameController::class,'index']);
        Route::group(['prefix'=>'frame'],function(){
            Route::get('/{id}',[MoodBoardFrameController::class,'get']);
            Route::put('/{id}',[MoodBoardFrameController::class,'update']);
            Route::delete('/{id}',[MoodBoardFrameController::class,'delete']);
            Route::post('/create',[MoodBoardFrameController::class,'create']);
        });
    });
});

// Auth Admin User
// Route::prefix('/admin')->as('admin.')->group(function () {
//     Route::prefix('/users')->as('users.')->group( function() {
//         Route::post('/login',[AdminLoginController::class], 'login');
        
//         Route::post('/register',[AdminRegisterController::class], 'register');
        
//         Route::post('password/forgot-password',[AdminForgotPasswordController::class], 'sendResetLinkResponse');
        
//         Route::post('password/reset',[AdminResetPasswordController::class], 'sendResetResponse');
        
//         Route::get('/google',[AdminLoginController::class], 'google');
        
//         Route::get('/google/redirect',[AdminLoginController::class], 'google_redirect');
        
//         Route::get('/apple',[AdminLoginController::class], 'apple');
        
//         Route::get('/apple', [AdminLoginController::class],'apple_redirect');
//     });

//     Route::prefix('/moodle')->as('moodle.')->group( function() {
//         Route::post('/login', [AdminMoodleLoginController::class],'login');
        
//         Route::post('/register',[AdminMoodleRegisterController::class], 'register');
//     });
// });

// // Auth Customer User
// Route::prefix('/users')->as('users.')->group( function() {
//     Route::post('/login', [LoginController::class],'login');
    
//     Route::post('/register',[RegisterController::class], 'register');

//     Route::post('password/forgot-password',[ForgotPasswordController::class], 'sendResetLinkResponse');

//     Route::post('password/reset', [ResetPasswordController::class],'sendResetResponse');
    
//     Route::get('/google/sign-in', [LoginController::class],'google');
    
//     Route::get('/google/sign-in/redirect',[LoginController::class], 'google_redirect');
    
//     Route::get('/apple/sign-in',[LoginController::class], 'apple');
    
//     Route::get('/apple/sign-in/redirect',[LoginController::class], 'apple_redirect');
// });

// // Auth Customer Moodle
// Route::prefix('/moodle')->as('moodle.')->group( function() {
//     Route::post('/login',[MoodleLoginController::class], 'login');
//     Route::post('/register',[MoodleRegisterController::class], 'register');
// });
// events

// subscription 
// plans
Route::prefix('/stripe')->group(function(){
    Route::get('/get_plans',[PlanController::class,'index']);
    Route::middleware("auth:sanctum")->group(function () {
        Route::get('/get_card_details/{id}',[StripeController::class,'get_card_details']);
    });
});

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('/subscription')->as('subscription.')->group(function() {
        Route::post('/check-subscription-status',[SubscriptionController::class], 'check_subscription_status');
        Route::post('/store', [SubscriptionController::class],'store');
        Route::put('/update', [SubscriptionController::class],'update');
        Route::post('/cancel', [SubscriptionController::class],'cancel');
    });

    Route::prefix('/plan')->as('plan.')->group(function() {
        Route::get('/',[PlanController::class], 'index');
        Route::get('/{plan}',[PlanController::class], 'show');
        Route::get('/test',[PlanController::class], 'test');
        Route::post('/store',[PlanController::class], 'store');
    });

    Route::prefix('/profile')->as('profile.')->group(function() {
        Route::get('/',[ProfileController::class], 'index');
        Route::put('/update', [ProfileController::class],'update');
    });

    Route::prefix('/suppliers')->as('suppliers.')->group(function() {
        Route::get('/',[SuppliersController::class] ,'index');
        Route::get('/{id}',[SuppliersController::class] ,'show');
        Route::post('/store',[SuppliersController::class] ,'store');
    });

    Route::prefix('/galleries')->as('galleries.')->group(function() {
        Route::get('/',[GalleriesController::class] ,'index');
        Route::get('/{id}',[GalleriesController::class] ,'show');
        Route::post('/store',[GalleriesController::class] ,'store');
    });

    Route::prefix('/mood-board')->group(function() {
        Route::get('/{id}/get',[MoodBoardController::class,'get_board']);
        Route::post('/create',[MoodBoardController::class,'create_mood_board']);
        Route::prefix('/items')->group(function(){
            Route::get('/',[MoodBoardController::class,'mood_board_item_list']);
            Route::get('/{id}/get',[MoodBoardController::class,'get_item']);
        });
        Route::prefix('/version')->group(function(){
            Route::post('/create',[MoodBoardController::class,'create_version']);
            Route::prefix('/items')->group(function(){
                // Route::get('/',[MoodBoardController::class,'get_version_item']);
                Route::post('/update',[MoodBoardController::class,'version_items_update']);
            });
            Route::prefix('/item')->group(function(){
                Route::get('/{id}/get',[MoodBoardController::class,'get_version_item']);
                Route::post('/add',[MoodBoardController::class,'version_add_item']);
            });
        });

        Route::post('/inspiration/update',[MoodBoardController::class,'update_inspiration_picture']);
        
        Route::get('/templates',[TemplateController::class,'index']);
        Route::prefix('template')->group(function(){
            Route::post('/create',[TemplateController::class,'create_board_using_template']);
        });

        Route::get('/backgrounds',[MoodBoardBackgroundController::class,'index']);
        Route::get('/frames',[MoodBoardFrameController::class,'index']);
    });
    Route::prefix('/mood-boards')->group(function() {
            Route::get('/',[MoodBoardController::class,'mood_board_list']);
            // Route::get('get-all-mood-board-contents', [MoodBoardController::class],'getAllMoodBoardContents');
            // Route::get('get-mood-board-content/{moodBoardId}',[MoodBoardController::class], 'getMoodBoardContent');
            // Route::post('/add-mood-board-content', [MoodBoardController::class],'addMoodBoardContent');
            // Route::put('/update-mood-board-content',[MoodBoardController::class], 'updateMoodBoardContent');
        
            // Route::get('/get-all-mood-board-images',[MoodBoardController::class], 'getAllMoodBoardImages');
            // Route::get('/get-mood-board-image/{imageId}', [MoodBoardController::class],'getMoodBoardImage');
            // Route::post('/add-mood-board-image', [MoodBoardController::class],'addMoodBoardImages');
        
            // Route::get('/get-all-mood-board-backgrounds', [MoodBoardController::class],'getAllMoodBoardBackgrounds');
            // Route::get('/get-mood-board-background/{backgroundId}', [MoodBoardController::class],'getMoodBoardBackground');
            // Route::post('/add-mood-board-background', [MoodBoardController::class],'addMoodBoardBackground');
       
        
            // Route::get('/get-all-mood-board-frames', [MoodBoardController::class],'getAllMoodBoardFrames');
            // Route::get('/get-mood-board-frame/{frameId}', [MoodBoardController::class],'getMoodBoardFrame');
            // Route::post('/add-mood-board-frame', [MoodBoardController::class],'addMoodBoardFrame');
            
        
    });
    Route::prefix('/event')->group(function() {
        Route::get('/{id}/get',[EventsController::class,'getEvent']);
        Route::get('/user-events',[EventsController::class,'getUserEvents']);
        Route::get('/user-event/{id}/get',[EventsController::class,'getUserEvent']);
        Route::get('/upcoming-events',[EventsController::class,'getUpcomingEvents']);
        Route::post('/user-events/cancel',[EventsController::class,'cancelAttendance']);
        Route::post('/user-events/book',[EventsController::class,'bookAttendance']);
    });
});
