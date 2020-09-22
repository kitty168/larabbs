<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\ReplyRequest;
use App\Models\Reply;
use App\Models\Topic;
use App\Transformers\ReplyTransformer;
use Illuminate\Http\Request;

class RepliesController extends Controller
{
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
