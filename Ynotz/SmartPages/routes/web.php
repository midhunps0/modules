<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RoleController;
use Modules\Ynotz\EasyAdmin\Services\RouteHelper;

Route::group(['middleware' => ['web', 'auth'], 'prefix' => '/manage'], function () {

    RouteHelper::getEasyRoutes(modelName: "User");
    RouteHelper::getEasyRoutes(modelName: "Role");
    RouteHelper::getEasyRoutes(modelName: "Permission");
});
?>
