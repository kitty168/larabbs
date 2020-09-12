<?php

namespace App\Http\Controllers;

use App\Handlers\ImageUploadHandler;
use App\Http\Requests\UserRequest;
use App\Models\User;

class UsersController extends Controller
{
    public function __construct()
    {
        // 引入auth 中间件，进行权限验证， except [不包括show]
        $this->middleware('auth', ['except' => ['show']]);
    }

    /**
     * 个人中心展示页面
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    /**
     * 个人资料编辑页面
     * @param User $user
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function edit(User $user)
    {
        // 这里 update 是指UserPolicy授权类里的 update 授权方法，$user 对应传参 update 授权方法的第二个参数
        $this->authorize('update',$user);

        return view('users.edit', compact('user'));
    }

    /**
     * 个人资料更新
     * @param UserRequest $request
     * @param ImageUploadHandler $imageUploadHandler
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(UserRequest $request, ImageUploadHandler $imageUploadHandler, User $user)
    {
        $this->authorize('update',$user);

        $data = $request->all();

        if ($request->avatar) {
            // 图片上传与图片裁剪
            $result = $imageUploadHandler->save($request->avatar, 'avatars', $user->id, 416);

            if ($result) {
                $data['avatar'] = $result['path'];
            }
        }

        // 保存数据
        $user->update($data);
        // with() 方法,将信息闪存到session中，用于消息提醒
        return redirect()->route('users.show', $user->id)->with('success', '个人资料更新成功！');
    }
}
