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

Auth::routes();

Route::get('/', 'HomeController@index')->name('home');
Route::get('/home', 'HomeController@index')->name('home');

Route::group(['middleware' => ['auth']], function () {
    // Dashboard
    Route::get('dashboard/{tahun_id}', 'DashboardController@index')->name('dashboard');

    // Profile
    Route::namespace('Profile')->group(function () {
        Route::resource('profile', 'ProfileController');
        Route::get('profile/{id}/edit-password', 'ProfileController@editPassword')->name('profile.editPassword');
        Route::post('profile/{id}/update-password', 'ProfileController@updatePassword')->name('profile.updatePassword');
    });

    // Master Roles
    Route::prefix('master-roles')->namespace('MasterRole')->name('master-role.')->group(function () {
        // Role
        Route::resource('role', 'RoleController');
        Route::prefix('role')->name('role.')->group(function () {
            Route::post('api', 'RoleController@api')->name('api');
            Route::get('{id}/addPermissions', 'RoleController@permission')->name('addPermissions');
            Route::post('storePermissions', 'RoleController@storePermission')->name('storePermissions');
            Route::get('{id}/getPermissions', 'RoleController@getPermissions')->name('getPermissions');
            Route::delete('{name}/destroyPermission', 'RoleController@destroyPermission')->name('destroyPermission');
        });
        // Permission
        Route::resource('permission', 'PermissionController');
        Route::post('permission/api', 'PermissionController@api')->name('permission.api');
    });

    // Pengguna
    Route::namespace('Pengguna')->group(function () {
        Route::resource('pengguna', 'PenggunaController');
        Route::post('pengguna/api', 'PenggunaController@api')->name('pengguna.api');
        Route::get('pengguna/edit-password/{id}', 'PenggunaController@editPassword')->name('pengguna.editPassword');
        Route::post('pengguna/update-password/{id}', 'PenggunaController@updatePassword')->name('pengguna.updatePassword');
        Route::get('pengguna/delete-verifikator-tempat/{id}', 'PenggunaController@deleteVerifikatorTempat')->name('pengguna.deleteVerifikatorTempat');
    });

    // Master Data
    Route::namespace('MasterData')->group(function () {
        // indikator
        Route::resource('indikator', 'IndikatorController');
        Route::post('indikator/api', 'IndikatorController@api')->name('indikator.api');

        // Quesioner
        Route::resource('kuesioner', 'QuesionerController');
        Route::post('kuesioner/api', 'QuesionerController@api')->name('kuesioner.api');
        Route::get('kuesioner/getPertanyaan/{id}', 'QuesionerController@getPertanyaan')->name('kuesioner.getPertanyaan');

        // Answer
        Route::resource('answer', 'AnswerController');
        Route::post('answer/api', 'AnswerController@api')->name('answer.api');

        // Pertanyaan
        Route::resource('pertanyaan', 'QuestionController');
        Route::post('pertanyaan/api', 'QuestionController@api')->name('pertanyaan.api');

        // Tempat
        Route::resource('perangkat-daerah', 'TempatController');
        Route::name('perangkat-daerah.')->group(function () {
            Route::post('api', 'TempatController@api')->name('api');
        });

        // Waktu
        Route::resource('waktu', 'TimeController');
        Route::post('waktu/api', 'TimeController@api')->name('waktu.api');
    });

    // Form Quesioner
    Route::namespace('Quesioner')->group(function () {
        Route::resource('form-quesioner', 'FormQuesionerController');
        // Hasil
        Route::get('hasil', 'DataQuesionerController@index')->name('hasil.index');
        Route::post('hasil/api', 'DataQuesionerController@api')->name('hasil.api');

        Route::get('hasil/show/{id}', 'DataQuesionerController@show')->name('hasil.show');
        Route::get('hasil/edit/{id}', 'DataQuesionerController@edit')->name('hasil.edit');
        Route::post('hasil/update/{id}', 'DataQuesionerController@update')->name('hasil.update');
        Route::get('hasil/deleteFile/{id}', 'DataQuesionerController@deleteFile')->name('hasil.deleteFile');
        Route::post('hasil/send-quesioner', 'DataQuesionerController@sendQuesioner')->name('hasil.sendQuesioner');

        Route::get('revisi', 'RevisiController@index')->name('revisi.index');
        Route::post('revisi/api', 'RevisiController@api')->name('revisi.api');
        Route::get('revisi/{id}', 'RevisiController@show')->name('revisi.show');
        Route::get('revisi/edit/{id}', 'RevisiController@edit')->name('revisi.edit');
        Route::get('revisi/deleteFile/{id}', 'RevisiController@deleteFile')->name('revisi.deleteFile');
        Route::post('revisi/update/{id}', 'RevisiController@update')->name('revisi.update');
        Route::post('revisi/send-quesioner', 'RevisiController@sendQuesioner')->name('revisi.sendQuesioner');
    });

    // Verifikasi
    Route::prefix('verifikasi')->name('verifikasi.')->namespace('MasterVerifikasi')->group(function () {
        //
        Route::get('perangkat-daerah', 'VerifikasiController@index')->name('index');
        Route::post('perangkat-daerah/api-perangkat-daerah', 'VerifikasiController@api')->name('api');

        Route::get('show', 'VerifikasiController@show')->name('show');
        Route::get('edit/{id}', 'VerifikasiController@edit')->name('edit');
        Route::get('confirm/{id}', 'VerifikasiController@confirm')->name('confirm');
        Route::get('updateRevisi/{id}', 'VerifikasiController@updateRevisi')->name('updateRevisi');
        Route::post('send-revisi/{id}', 'VerifikasiController@sendRevisi')->name('sendRevisi');

        Route::get('report', 'VerifikasiController@report')->name('report');
        Route::get('kirim-revisi', 'VerifikasiController@kirimRevisi')->name('kirimRevisi');

        Route::get('batalkan-verifikasi/{id}', 'VerifikasiController@batalkanVerifikasi')->name('batalkanVerifikasi');
    });

    // Status Pengisian
    Route::prefix('statusPengisian')->name('statusPengisian.')->namespace('MasterStatusPengisian')->group(function () {
        //
        Route::get('data/{zona_id}', 'StatuPengisianController@index')->name('data');
        Route::post('data/api', 'StatuPengisianController@api')->name('api');
    });
});

Route::get('check/{jenis}', 'HomeController@checkDuplicate');
