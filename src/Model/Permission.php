<?php

namespace lion9966\Permission\Model;

use think\Model;
use lion9966\Permission\Contract\PermissionContract;

/**
 * 权限
 */
class Permission extends Model implements PermissionContract
{
    use \lion9966\Permission\Traits\Permission;
}
