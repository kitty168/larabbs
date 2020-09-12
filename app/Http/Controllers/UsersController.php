<?php

namespace App\Http\Controllers;

use App\Handlers\ImageUploadHandler;
use App\Http\Requests\UserRequest;
use App\Models\User;

class UsersController extends Controller
{
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
     */
    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    /**
     * 个人资料更新
     * @param UserRequest $request
     * @param ImageUploadHandler $imageUploadHandler
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UserRequest $request, ImageUploadHandler $imageUploadHandler, User $user)
    {
        $data = $request->all();

        if ($request->avatar) {
            $result = $imageUploadHandler->save($request->avatar, 'avatars', $user->id);

            if ($result) {
                $data['avatar'] = $result['path'];
            }
        }

        $user->update($data);
        // with() 方法,将信息闪存到session中，用于消息提醒
        return redirect()->route('users.show', $user->id)->with('success', '个人资料更新成功！');
    }
}
