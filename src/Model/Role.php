<?php

namespace lion9966\Permission\Model;

use think\Model;
use lion9966\Permission\Contract\RoleContract;

/**
 * 角色
 */
class Role extends Model implements RoleContract
{
    use \lion9966\Permission\Traits\Role;
}
