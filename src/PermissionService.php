<?php

namespace lion9966\Permission;

use lion9966\Permission\command\Install as PermissionInstall;
use lion9966\Permission\Middleware\Permission;
use lion9966\Permission\Middleware\Role;

/**
 * 权限服务
 */
class PermissionService extends \think\Service
{
    public function register()
    {
        $this->app->bind('auth', Permission::class);
        $this->app->bind('auth.permission', Permission::class);
        $this->app->bind('auth.role', Role::class);
    }

    public function boot()
    {
        $this->commamds([
            PermissionInstall::class,
        ]);
    }
}
