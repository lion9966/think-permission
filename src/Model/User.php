<?php

namespace lion9966\Permission\Model;

use think\Model;
use lion9966\Permission\Contract\UserContract;

/**
 * 用户
 */
class User extends Model implements UserContract
{
    use \lion9966\Permission\Traits\User;
}
