<?php

namespace lion9966\Permission\Contract;

use think\Request;
use think\Response;

interface RoleMiddlewareContract
{
    /**
     * 检查是否有权限.
     *
     * @param Request $request
     * @param UserContract $user
     * @param string $role
     *
     * @return bool
     */
    public function requestHasRole(Request $request, UserContract $user, string $role);

    /**
     * 用户没有权限.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function handleNoAuthority(Request $request): Response;

    /**
     * 用户未登录.
     *
     * @param Request $request
     *
     * @return Response
     */
    public function handleNotLoggedIn(Request $request): Response;

}
