<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


// AUTH
Route::name('appAuth.')->group(function () {
    Route::group(['prefix' => 'auth'], function() {
        Route::post('/signup', [ApiController::class, 'signUp'])->name('signUp');
        Route::post('/signin', [ApiController::class, 'signIn'])->name('signIn');
        Route::post('/signout', [ApiController::class, 'signOut'])->name('signOut');
        Route::post('/checkAuth', [ApiController::class, 'checkAuth'])->name('checkAuth');
    });
});

// MY ACCOUNT
Route::name('myAccount.')->group(function () {
    Route::group(['prefix' => 'myAccount'], function() {
        Route::post('/updateProfile', [ApiController::class, 'updateProfile'])->name('updateProfile');
        Route::post('/deleteProfileImage', [ApiController::class, 'deleteProfileImage'])->name('deleteProfileImage');
        Route::post('/changePassword', [ApiController::class, 'changePassword'])->name('changePassword');
        Route::post('/changeUsername', [ApiController::class, 'changeUsername'])->name('changeUsername');
        Route::post('/changeEmail', [ApiController::class, 'changeEmail'])->name('changeEmail');
    });
});


// CATEGORY
Route::name('category.')->group(function () {
    Route::group(['prefix' => 'categories'], function() {
        Route::get('/', [ApiController::class, 'allCategories'])->name('all');
        Route::post('/add', [ApiController::class, 'addCaregory'])->name('add');
        Route::get('/edit/{id}', [ApiController::class, 'editCategory'])->name('edit');
        Route::post('/update/{id}', [ApiController::class, 'updateCaregory'])->name('update');
        Route::post('/delete', [ApiController::class, 'deleteCategory'])->name('delete');
        Route::post('/changeStatus', [ApiController::class, 'changeCategoryStatus'])->name('changeStatus');
        Route::post('/changeVisibility', [ApiController::class, 'changeCategoryVisibility'])->name('changeVisibility');
        Route::post('/bulkDelete', [ApiController::class, 'bulkDeleteCategory'])->name('bulkDelete');
    });
});


// POST
Route::name('postbox.')->group(function () {
    Route::group(['prefix' => 'postboxes'], function() {
        Route::get('/', [ApiController::class, 'allPostBoxes'])->name('all');
        Route::post('/add', [ApiController::class, 'addPostBox'])->name('add');
        Route::get('/edit/{id}', [ApiController::class, 'editPostBox'])->name('edit');
        Route::post('/update/{id}', [ApiController::class, 'updatePostBox'])->name('update');
        Route::post('/delete', [ApiController::class, 'deletePostBox'])->name('delete');
        Route::post('/changeStatus', [ApiController::class, 'changePostBoxStatus'])->name('changeStatus');
        Route::post('/bulkDelete', [ApiController::class, 'bulkDeletePostBox'])->name('bulkDelete');
    });
});

// KEYWORDS
Route::name('keyword.')->group(function () {
    Route::group(['prefix' => 'keywords'], function() {
        Route::get('/', [ApiController::class, 'allKeywords'])->name('all');
    });
});

// TASKS
Route::name('task.')->group(function () {
    Route::group(['prefix' => 'tasks'], function() {
        Route::get('/', [ApiController::class, 'getAllTasks'])->name('all');
        Route::get('/view/{id}', [ApiController::class, 'getTaskInfo'])->name('view');
        Route::post('/add', [ApiController::class, 'createTask'])->name('add');
        Route::post('/status', [ApiController::class, 'changeStatus'])->name('status');
        Route::post('/subtask-status', [ApiController::class, 'changeSubTaskStatus'])->name('subtaskStatus');
        Route::post('/delete', [ApiController::class, 'deleteTask'])->name('delete');
        Route::post('/bulkDelete', [ApiController::class, 'bulkDeleteTask'])->name('bulkDelete');
        Route::post('/update/{id}', [ApiController::class, 'updateTask'])->name('update');
    });
});


// NOTES
Route::name('note.')->group(function () {
    Route::group(['prefix' => 'notes'], function() {
        Route::get('/', [ApiController::class, 'getNotes'])->name('all');
        Route::get('/view/{slug}', [ApiController::class, 'getNoteInfoBySlug'])->name('view');
        Route::post('/add', [ApiController::class, 'createNote'])->name('add');
        Route::get('/edit/{id}', [ApiController::class, 'getNoteInfoById'])->name('edit');
        Route::post('/update/{id}', [ApiController::class, 'updateNote'])->name('update');
        Route::post('/delete', [ApiController::class, 'deleteNote'])->name('delete');
        Route::post('/bulkDelete', [ApiController::class, 'bulkDeleteNote'])->name('bulkDelete');
    });
});

// DASHBOARD
Route::name('dash.')->group(function () {
    Route::group(['prefix' => 'dashboard'], function() {
        Route::get('/counts', [ApiController::class, 'getAllCounts'])->name('allCounts');
    });
});


Route::post('/check-slug/existOrNot', [ApiController::class, 'checkSlug'])->name('checkSlug');

// ROOT
Route::get('/{any?}', function () {
    return view('app');
})->where('any', '[\/\w\.-]*');

