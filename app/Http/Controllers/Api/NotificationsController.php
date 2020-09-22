<?php

namespace App\Http\Controllers\Api;

use App\Transformers\NotificationTransformer;
use Illuminate\Http\Request;

class NotificationsController extends Controller
{
    /**
     * 我的通知列表
     * @return \Dingo\Api\Http\Response
     */
    public function index()
    {
        // 用户模型的 notifications 方法是 Laravel 的消息通知系统 为我们提供的方法，按通知创建时间倒叙排序。
        $notifications = $this->user->notifications()->paginate(10);

        return $this->response->paginator($notifications, new NotificationTransformer());
    }
}
