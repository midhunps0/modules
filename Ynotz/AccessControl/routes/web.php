<?php

use Illuminate\Support\Facades\Route;
use Modules\Ynotz\AccessControl\Http\Controllers\PermissionsController;
use Modules\Ynotz\AccessControl\Http\Controllers\RolesController;
use Modules\Ynotz\AccessControl\Http\Controllers\UsersController;

Route::group(['middleware' => ['web', 'auth'], 'prefix' => 'manage'], function () {
    Route::get('/roles/select-ids', [RolesController::class, 'selectIds'])->name('roles.selectIds');
    Route::get('/roles/suggest-list', [RolesController::class, 'suggestlist'])->name('roles.suggestlist');
    Route::get('/roles/download', [RolesController::class, 'download'])->name('roles.download');
    Route::resource('roles', RolesController::class);
    Route::get('/permissions/select-ids', [PermissionsController::class, 'selectIds'])->name('permissions.selectIds');
    Route::resource('permissions', PermissionsController::class);
    Route::get('/users/select-ids', [UsersController::class, 'selectIds'])->name('users.selectIds');
    Route::get('users/download', [UsersController::class, 'download'])->name('users.download');
    Route::resource('users', UsersController::class);
});

?>
