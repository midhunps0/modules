<?php

namespace Modules\Ynotz\EasyAdmin\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Ynotz\SmartPages\Http\Controllers\SmartController;
use Modules\Ynotz\EasyAdmin\Services\DashboardServiceInterface;

class DashboardController extends SmartController
{
    public function dashboard(Request $request, DashboardServiceInterface $dashboardService)
    {
        // dd(config('easyadmin.dashboard_view'));
        return $this->buildResponse(
            config('easyadmin.dashboard_view'),
            [
                'dashboard_data' => $dashboardService->getDashboardData($request->all()),
                'sidebar_data' => config('easyadmin.dashboard_sidebar')
            ]);
    }
}
