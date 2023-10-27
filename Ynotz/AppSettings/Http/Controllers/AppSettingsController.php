<?php

namespace Modules\Ynotz\AppSettings\Http\Controllers;

use Throwable;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Modules\Ynotz\EasyAdmin\Traits\HasMVConnector;
use Modules\Ynotz\AppSettings\Services\AppSettingService;
use Illuminate\Auth\Access\AuthorizationException;
use Modules\Ynotz\AppSettings\Models\AppSetting;
use Modules\Ynotz\SmartPages\Http\Controllers\SmartController;

class AppSettingsController extends SmartController
{
    use HasMVConnector;

    public function __construct(public AppSettingService $connectorService, Request $request){
        parent::__construct($request);
    }

    public function edit($id)
    {
        $setting = AppSetting::find($id);
        // dd($setting->value_type);
        if($setting->value_type == 'json') {
            $view = 'ynotz_appsettings::edit_'.Str::snake($setting->slug);
        } else {
            $view = $this->editView ?? 'admin.'.Str::plural($this->getItemName()).'.edit';
        }
        try {
            if(!$this->connectorService->authoriseEdit($id)) {
                throw new AuthorizationException('User not authorised to perform this task');
            }
            $data =$setting->value_type == 'json' ? ['setting' => $setting] :$this->connectorService->getEditPageData($id)->getData();
            return $this->buildResponse($view, $data);
        } catch (AuthorizationException $e) {
            info($e);
            return $this->buildResponse($this->unauthorisedView);
        } catch (Throwable $e) {
            info($e);
            return $this->buildResponse($this->errorView, ['error' => $e->__toString()]);
        }
    }
}
