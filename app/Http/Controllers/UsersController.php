<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;

class UsersController extends Controller
{
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(UserRequest $request,User $user)
    {
        $user->update($request->all());
        // with() 方法,将信息闪存到session中，用于消息提醒
        return redirect()->route('users.show', $user->id)->with('success', '个人资料更新成功！');
    }
}
