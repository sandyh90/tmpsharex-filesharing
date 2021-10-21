<?php

use Illuminate\Support\Facades\Route;

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
//Hide File Route
Route::prefix('storage')->group(function () {
    Route::get('/userimg/{name}', [App\Http\Controllers\System\SystemFile::class, 'user_image'])->name('user_img');
});

Route::get('/', [App\Http\Controllers\Front::class, 'home'])->name('home');
Route::get('/fileslist', [App\Http\Controllers\Front::class, 'files_list'])->name('home.fileslist');
Route::get('/search', [App\Http\Controllers\Front::class, 'search_files'])->name('home.searchfiles');
Route::group(['middleware' => ['only.ajax'], 'prefix' => 'ajax'], function () {
    Route::get('/fresh_captcha', [App\Http\Controllers\Front::class, 'regen_captcha'])->name('home.regen_captcha');
});


Route::prefix('download')->group(function () {
    Route::redirect('/', '/');
    Route::get('/info/{uuid_file}/{id_file}', [App\Http\Controllers\Download_Page::class, 'get_file'])->name('download.getfile');
    Route::post('/delete_file/{uuid_file}/{id_file}', [App\Http\Controllers\Download_Page::class, 'delete_file'])->name('download.deletefile');
    Route::post('/process/{uuid_file}/{id_file}', [App\Http\Controllers\Download_Page::class, 'process_download'])->name('download.processfile');
    Route::get('/getfile/{filename}', [App\Http\Controllers\Download_Page::class, 'download_file'])->name('download.downloadfile')->middleware('signed');
});

Route::group(['middleware' => 'limitupload.auth', 'prefix' => 'upload'], function () {
    Route::get('/', [App\Http\Controllers\Upload_Page::class, 'upload'])->name('upload.upload_page');

    Route::group(['middleware' => ['only.ajax'], 'prefix' => 'ajax'], function () {
        Route::post('/upload', [App\Http\Controllers\Upload_Page::class, 'upload_files'])->name('upload.upload_module');
    });
});

Route::group(['middleware' => 'guest'], function () {
    Route::get('/login', [App\Http\Controllers\Auth\AuthController::class, 'showlogin'])->name('login');
    Route::post('/login', [App\Http\Controllers\Auth\AuthController::class, 'login'])->name('login.process');

    Route::get('/register', [App\Http\Controllers\Auth\RegisterController::class, 'showregister'])->name('register');
    Route::post('/register', [App\Http\Controllers\Auth\RegisterController::class, 'register'])->name('register.process');
});

Route::group(['middleware' => 'auth'], function () {
    Route::post('logout', [App\Http\Controllers\Auth\AuthController::class, 'logout'])->name('logout');

    Route::prefix('settings')->group(function () {
        Route::get('/', [App\Http\Controllers\User\MySettings::class, 'mysettings'])->name('user.settings');
        Route::post('/profile_change_data', [App\Http\Controllers\User\MySettings::class, 'update_profile'])->name('settings.update_profile');
        Route::post('/password_change_data', [App\Http\Controllers\User\MySettings::class, 'update_password'])->name('settings.update_password');
        Route::post('/logout_all_session', [App\Http\Controllers\User\MySettings::class, 'logout_all_session'])->name('settings.logout_all');
    });

    Route::get('/myfiles', [App\Http\Controllers\User\MyFiles::class, 'myfiles'])->name('myfiles.page');

    Route::group(['middleware' => ['only.ajax'], 'prefix' => 'ajax'], function () {
        Route::get('/my_file_list', [App\Http\Controllers\User\MyFiles::class, 'my_file_list'])->name('myfiles.ajaxlist');
        Route::post('/my_file_edit', [App\Http\Controllers\User\MyFiles::class, 'my_file_edit'])->name('myfiles.ajaxedit');
        Route::post('/my_file_delete', [App\Http\Controllers\User\MyFiles::class, 'my_file_delete'])->name('myfiles.ajaxdelete');
    });

    Route::group(['middleware' => ['role:administrator'], 'prefix' => 'admin'], function () {
        Route::get('/', [App\Http\Controllers\Admin\Dashboard::class, 'dashboard'])->name('admin.dashboard');

        Route::prefix('users')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\UserManage::class, 'manage_user'])->name('user_manage.page');
            Route::get('/data', [App\Http\Controllers\Admin\UserManage::class, 'get_user'])->name('user_manage.data');
            Route::post('/add', [App\Http\Controllers\Admin\UserManage::class, 'add_user'])->name('user_manage.add');
            Route::post('/edit', [App\Http\Controllers\Admin\UserManage::class, 'edit_user'])->name('user_manage.edit');
            Route::post('/delete', [App\Http\Controllers\Admin\UserManage::class, 'delete_user'])->name('user_manage.delete');
        });

        Route::prefix('files')->group(function () {
            Route::get('/', [App\Http\Controllers\Admin\FilesManage::class, 'manage_files'])->name('files_manage.page');
            Route::get('/data', [App\Http\Controllers\Admin\FilesManage::class, 'get_files'])->name('files_manage.data');
            Route::post('/delete', [App\Http\Controllers\Admin\FilesManage::class, 'delete_files'])->name('files_manage.delete');
        });
    });
});

/**
 * If route not available in list laravel redirect to
 * 404 page to prevent showing error
 */

Route::fallback(function () {
    return abort(404);
});
