<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ReplyRequest;
use App\Models\Reply;
use App\Models\Topic;
use App\Models\User;
use App\Transformers\ReplyTransformer;
use Illuminate\Http\Request;

class RepliesController extends Controller
{
    /**
     * 话题的回复列表
     * @param Topic $topic
     * @return \Dingo\Api\Http\Response
     */
    public function index(Topic $topic)
    {
        $replies = $topic->replies()->paginate(10);

        return $this->response->paginator($replies, new ReplyTransformer());
    }

    /**
     * 用户的回复列表
     * @param User $user
     * @return \Dingo\Api\Http\Response
     */
    public function userIndex(User $user)
    {
        // 复杂的关联查询可以临时关闭 Dingo 的预加载， 手动处理
        // app(\Dingo\Api\Transformer\Factory::class)->disableEagerLoading();
        // $replies->load(explode(',', $request->include));

        $replies = $user->replies()->paginate(10);

        return $this->response->paginator($replies, new ReplyTransformer());
    }

    /**
     * 添加回复
     * @param ReplyRequest $request
     * @param Topic $topic
     * @param Reply $reply
     * @return \Dingo\Api\Http\Response
     */
    public function store(ReplyRequest $request, Topic $topic, Reply $reply)
    {
        $reply->content = $request->input('content');
        // $reply->user_id = $this->user()->id;
        // $reply->topic_id = $topic->id;

        // 关联的写法
        $reply->topic()->associate($topic);
        $reply->user()->associate($this->user());

        $reply->save();

        return $this->response->item($reply, new ReplyTransformer())->setStatusCode(201);

    }

    /**
     * 删除回复
     * @param Topic $topic
     * @param Reply $reply
     * @return \Dingo\Api\Http\Response|void
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Topic $topic, Reply $reply)
    {
        if($reply->topic_id != $topic->id) {
            return $this->response->errorBadRequest();
        }
        // 权限验证
        $this->authorize('destroy', $reply);

        $reply->delete();

        return $this->response->noContent();
    }
}
