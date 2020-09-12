<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * 用户授权策略类
 * Class UserPolicy
 * @package App\Policies
 */
class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * 用户更新时的权限验证
     * =====================
     * 使用授权策略需要注意以下两点：
     * 我们并不需要检查 $currentUser 是不是 NULL。未登录用户，框架会自动为其 所有权限 返回 false；
     * 调用时，默认情况下，我们 不需要 传递当前登录用户至该方法内，因为框架会自动加载当前登录用户（接着看下去，后面有例子）；
     * =====================
     *
     * @param User $currentUser 当前用户
     * @param User $user 进行授权的用户
     * @return bool
     */
    public function update(User $currentUser, User $user)
    {
        return $currentUser->id === $user->id;
    }
}
