<?php
namespace Modules\Ynotz\EasyAdmin\Services;

use Modules\Ynotz\EasyAdmin\Contracts\SidebarServiceInterface;

class SidebarService implements SidebarServiceInterface
{
    public function getSidebarData(): array
    {
        return [
            [
                'type' => 'menu_item',
                'title' => 'Dashboard',
                'route' => 'dashboard',
                'route_params' => [],
                'icon' => 'easyadmin::icons.users',
                'show' => true
            ],
            // [
            //     'type' => 'menu_group',
            //     'title' => 'Access Control',
            //     'icon' => 'easyadmin::icons.users',
            //     'show' => $this->showRoles(),
            //     'menu_items' => [
            //         [
            //             'type' => 'menu_item',
            //             'title' => 'Users',
            //             'route' => 'users.index',
            //             'route_params' => [],
            //             'icon' => 'easyadmin::icons.users',
            //             'show' => $this->showRoles()
            //         ],
            //         [
            //             'type' => 'menu_item',
            //             'title' => 'Roles',
            //             'route' => 'roles.index',
            //             'route_params' => [],
            //             'icon' => 'easyadmin::icons.users',
            //             'show' => $this->showRoles()
            //         ],
            //         [
            //             'type' => 'menu_item',
            //             'title' => 'Permissions',
            //             'route' => 'permissions.index',
            //             'route_params' => [],
            //             'icon' => 'easyadmin::icons.users',
            //             'show' => $this->showPermissions()
            //         ],
            //     ]
            // ],

            // [
            //     'type' => 'menu_section',
            //     'title' => 'Menu Group',
            //     'icon' => 'easyadmin::icons.gear',
            //     'show' => $this->showRoles(),
            //     'menu_items' => [
            //         [
            //             'type' => 'menu_item',
            //             'title' => 'Menu Item Two',
            //             'route' => 'home',
            //             'route_params' => [],
            //             'icon' => 'easyadmin::icons.plus',
            //             'show' => $this->showRoles()
            //         ],
            //     ]
            // ],
            // [
            //     'type' => 'menu_item',
            //     'title' => 'Menu Item Two',
            //     'route' => 'home',
            //     'route_params' => [],
            //     'icon' => 'easyadmin::icons.plus',
            //     'show' => $this->showRoles()
            // ],
        ];
    }

    private function showRoles()
    {
        return auth()->check();
    }
    private function showPermissions()
    {
        return auth()->check();
    }
}
?>
