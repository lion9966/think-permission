<?php

namespace lion9966\Permission\Middleware;

use lion9966\Permission\Contract\RoleMiddlewareContract;
use lion9966\Permission\Contract\UserContract;
use think\Request;
use think\Response;
use lion9966\exception\ResponseCode;

/**
 * 角色权限中间件.
 */
class Role implements RoleMiddlewareContract
{
    public function handle($request, \Closure $next, $role)
    {
        if (!$request->user) {
            return $this->handleNotLoggedIn($request);
        }
        if (false === $this->requestHasRole($request, $request->user, $role)) {
            return $this->handleNoAuthority($request);
        }
        return $next($request);
    }

    /**
     * {@inheritdoc}
     * @return bool
     */
    public function requestHasRole(Request $request, UserContract $user, string $role): bool
    {
        if ($user->hasRole($role)) {
            return false;
        }
        return true;
    }

    /**
     * {@inheritdoc}
     * @return Response
     */
    public function handleNotLoggedIn(Request $request): Response
    {
        //return Response::create(['message' => '用户未登录', 'code' => 101], 'json', 400);
        //增加use lion9966\exception\ResponseCode后采用以下方式
        //增加语言lang目录，
        //且需要手动加载语言包
        //$this->lang($lang,'User is not login');
        return Response::create(['message' => $this->lang('User is not login'), 'code' => ResponseCode::LOGIN_ERROR], 'json', 400);
    }

    /**
     * {@inheritdoc}
     * @return Response
     */
    public function handleNoAuthority(Request $request): Response
    {
        //return Response::create(['message' => '没有权限', 'code' => 105], 'json', 400);
        //增加语言lang目录，
        //且需要手动加载语言包
        //$this->lang($lang,'Do not have permission');
        return Response::create(['message' => $this->lang('Do not have permission'), 'code' => ResponseCode::AUTH_ERROR], 'json', 400);
    }

    /**
     * 语言载入
     * @param $name
     * @return string
     */
    protected function lang($name): string
    {
        if (!Lang::has($name)) {
            $getLangSet = Lang::getLangSet();
            Lang::load([app_path() . DIRECTORY_SEPARATOR . 'lion9966' . DIRECTORY_SEPARATOR . 'permission' . DIRECTORY_SEPARATOR . 'Lang' . DIRECTORY_SEPARATOR . $getLangSet . '.php']);
        }
        return Lang::get($name);

    }
}
