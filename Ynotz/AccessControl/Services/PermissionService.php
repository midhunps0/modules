<?php
namespace Modules\Ynotz\AccessControl\Services;


use Modules\Ynotz\AccessControl\Models\Permission;
use Modules\Ynotz\EasyAdmin\Traits\IsModelViewConnector;
use Modules\Ynotz\EasyAdmin\Contracts\ModelViewConnector;

class PermissionService implements ModelViewConnector
{
    use IsModelViewConnector;

    public function __construct()
    {
        $this->modelClass = Permission::class;
    }

    protected function getRelationQuery(int $id = null) {
        return null;
    }

    protected function accessCheck($item): bool
    {
        return true;
    }
}
?>
