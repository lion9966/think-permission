# think-permission

thinkphp 6 think-permission 权限管理

### 安装

```
composer require lion9966/think-permission
```

### 使用

* [创建必要数据](#创建必要数据)
*
    * [规则](#规则)
*
    * [角色](#角色)
*
    * [用户](#用户)
* [分配关系](#分配关系)
*
    * [规则与角色](#规则与角色)
*
    * [用户与角色](#用户与角色)
* [解除关系](#解除关系)
*
    * [规则与角色](#解除规则与角色)
*
    * [用户与角色](#解除用户与角色)
* [权限判断](#权限判断)
* [数据表](#数据表)

### 创建必要数据

#### 规则

```php
use lion9966\Permission\Model\Permission;
// 创建一条可查看首页的权限 
Permission::create(['name' => 'home']);
```

#### 角色

```php
use lion9966\Permission\Model\Role;
// 创建一个名为编辑的角色
Role::create(['name' => 'writer']);
```

#### 用户

```php
use lion9966\Permission\Model\User;
// 创建一个名为lion9966的用户
User::create(['name' => 'lion9966']);
```

### 分配关系

#### 规则与角色

```php

use lion9966\Permission\Model\Permission;
use lion9966\Permission\Model\Role;
// 将home规则分配到writer角色 
$permission = Permission::findByName('home');
$role = Permission::findByName('writer');
$permission->assignRole($role);

// 将home规则分配到writer角色 (跟上面效果一样)
$permission = Permission::findByName('home');
$role = Permission::findByName('writer');
$role->assignPermission($permission);
```

#### 用户与角色

```php

use lion9966\Permission\Model\User;
use lion9966\Permission\Model\Role;

// 为用户lion9966分配 writer角色 
$user = User::findByName('lion9966');
$role = Permission::findByName('writer');
$user->assignRole($role);

// 为用户lion9966分配 writer角色 (跟上面效果一样)
$user = User::findByName('lion9966');
$role = Permission::findByName('writer');
$role->assignUser($user);

```

### 解除关系

#### 解除规则与角色

```php
use lion9966\Permission\Model\Permission;
use lion9966\Permission\Model\Role;

// home规则与writer角色 解除关系
$permission = Permission::findByName('home');
$role = Permission::findByName('writer');
$permission->removeRole($role);

// writer角色与home规则 解除关系(跟上面效果一样)
$permission = Permission::findByName('home');
$role = Permission::findByName('writer');
$role->removePermission($permission);
```

#### 解除用户与角色

```php

use lion9966\Permission\Model\User;
use lion9966\Permission\Model\Role;

// 用户lion9966与writer角色 解除关系
$user = User::findByName('lion9966');
$role = Permission::findByName('writer');
$user->removeRole($role);

// writer角色与用户lion9966 解除关系 (跟上面效果一样)
$user = User::findByName('lion9966');
$role = Permission::findByName('writer');
$role->removeUser($user);

```

### 权限判断

#### 手动

```php
use lion9966\Permission\Model\User;

$user = User::findByName('lion9966');
if ($user->can('home')) {
    // 有 `home`权限
} else {
    // 无 `home`权限
}
```

#### 路由守护

用户信息，请手动注入到`$request->user`上，并且使用 `\lion9966\Permission\Contract\UserContract` 接口。 [Demo](#中间件注入用户)
`/home` 路由添加一条权限控制 访问者有 `home`权限才能允许访问

```php
Route::post('/home', 'home/index')->middleware('auth', 'home');
```

### 中间件注入用户

#### 用户模型

```php
<?php

namespace app\model;

use think\Request;
use lion9966\Permission\Contract\UserContract;

class User implements UserContract
{
    use \lion9966\Permission\Traits\User;
}
```

#### 注入用户信息

```php
<?php

namespace app\middleware;

use think\Request;
use app\model\User;

class Auth
{
    public function handle($request, \Closure $next)
    {
        $uid = 1;
        $user = User::find($uid);

        $request->user = $user;
        return $next($request);
    }
}

```

### 数据表

* `permission`

```mysql
CREATE TABLE `permission`
(
    `id`   int(11)      NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL COMMENT '规则唯一标识',
    PRIMARY KEY (`id`),
    UNIQUE KEY `name` (`name`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8;
```

* `role`

```mysql
CREATE TABLE `role`
(
    `id`   int(11)      NOT NULL AUTO_INCREMENT,
    `name` varchar(100) NOT NULL COMMENT '角色唯一标识',
    PRIMARY KEY (`id`),
    UNIQUE KEY `name` (`name`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8;
```

* `role-permission-access`

```mysql
CREATE TABLE `role_permission_access`
(
    `role_id`       int(11) NOT NULL COMMENT '角色主键',
    `permission_id` int(11) NOT NULL COMMENT '规则主键',
    PRIMARY KEY (`role_id`, `permission_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
```

* `user`

```mysql
CREATE TABLE `user`
(
    `id`   int(11)     NOT NULL AUTO_INCREMENT,
    `name` varchar(50) NOT NULL COMMENT '用户唯一标识',
    PRIMARY KEY (`id`),
    UNIQUE KEY `name` (`name`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 1
  DEFAULT CHARSET = utf8;
```

* `user_role_access`

```mysql
CREATE TABLE `user_role_access`
(
    `user_id` int(11) NOT NULL COMMENT '用户主键',
    `role_id` int(11) NOT NULL COMMENT '角色主键',
    PRIMARY KEY (`user_id`, `role_id`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8;
```

### 中间件

默认自带以下中间件

- 规则中间件
- 角色中间件

规则中间件

```php
#route/app.php

# 拥有 edit 规则的用户 可以访问此路由
Route::rule('/testPermission', function(){
  return 'edit';
}, 'GET')->allowCrossDomain()->middleware('auth.permission', 'edit');
```

角色中间件

```php
#route/app.php

# 拥有 editer 角色的用户 可以访问此路由
Route::rule('/testRole', function(){
  return 'editer';
}, 'GET')->allowCrossDomain()->middleware('auth.role', 'editer');

```
