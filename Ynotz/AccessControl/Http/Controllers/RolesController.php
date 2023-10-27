<?php

namespace Modules\Ynotz\AccessControl\Http\Controllers;

use Illuminate\Http\Request;
use Modules\Ynotz\AccessControl\Models\Role;
use Modules\Ynotz\EasyAdmin\Traits\HasMVConnector;
use Modules\Ynotz\AccessControl\Services\RoleService;
use Modules\Ynotz\SmartPages\Http\Controllers\SmartController;
use Modules\Ynotz\AccessControl\Http\Requests\RolesStoreRequest;

class RolesController extends SmartController
{
    use HasMVConnector;

    public function __construct(RoleService $connectorService, Request $request){
        parent::__construct($request);
        $this->connectorService = $connectorService;
        $this->itemName = 'role';
        $this->indexView = 'easyadmin::admin.indexpanel';
        $this->createView = 'accesscontrol::roles.create';
        $this->editView = 'accesscontrol::roles.edit';
        $this->modelClass = Role::class;
    }

    public function store(RolesStoreRequest $request)
    {
        return $this->doStore($request);
    }
}
