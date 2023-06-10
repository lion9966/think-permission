<?php

namespace lion9966\Permission\Traits;

use think\model\relation\BelongsToMany;
use lion9966\Permission\Contract\PermissionContract;
use lion9966\Permission\Contract\UserContract;

trait Role
{
    /**
     * 获取角色下所有权限
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.permission.model'),
            config('permission.role_permission_access'),
            config('permission.permission.froeign_key'),
            config('permission.role.froeign_key')
        );
    }

    /**
     * 获取角色下所有用户
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            config('permission.user.model'),
            config('permission.user_role_access'),
            config('permission.user.froeign_key'),
            config('permission.role.froeign_key')
        );
    }

    /**
     * 为当前角色分配一个权限
     * @param PermissionContract $permission
     */
    public function assignPermission(PermissionContract $permission)
    {
        $this->permissions()->attach($permission);
    }

    /**
     * 为当前角色移除一个权限
     * @param PermissionContract $permission
     */
    public function removePermission(PermissionContract $permission)
    {
        $this->permissions()->detach($permission);
    }

    /**
     * 为当前角色移除所有权限
     */
    public function removeAllPermission()
    {
        $permissions = $this->permissions;

        foreach ($permissions as $permission) {
            $this->removePermission($permission);
        }
    }

    /**
     * 将当前角色分配到指定用户
     * @param UserContract $user
     */
    public function assignUser(UserContract $user)
    {
        $this->users()->attach($user);
    }

    /**
     * 角色与用户解除关系
     * @param UserContract $user
     */
    public function removeUser(UserContract $user)
    {
        $this->users()->detach($user);
    }

    /**
     * 通过名称查找角色
     * @param $name
     * @return mixed
     */
    public static function findByName($name)
    {
        return self::where(['name' => $name])->find();
    }
}
