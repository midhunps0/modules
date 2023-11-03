<?php

namespace Modules\Ynotz\EasyAdmin\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Modules\Ynotz\SmartPages\Http\Controllers\SmartController;
use Modules\Ynotz\EasyAdmin\Services\DashboardServiceInterface;
use Modules\Ynotz\EasyAdmin\Services\ImageService;
use Modules\Ynotz\EasyAdmin\InputUpdateResponse;

class MasterController extends SmartController
{
    private $data;

    public function fetch($service, $method)
    {
        try {
            $this->setData(
                (app()->make(Str::replace('::', '\\', $service)))->$method($this->request->all())
            );
            return response()->json([
                'success' => true,
                'results' => $this->data->result,
                'message' => $this->data->message,
                'isvalid' => $this->data->isvalid
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error' => $e->__toString()
            ]);
        }
    }

    public function setData(InputUpdateResponse $data): void
    {
        $this->data = $data;
    }
}
