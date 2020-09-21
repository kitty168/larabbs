<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\TopicRequest;
use App\Models\Topic;
use App\Models\User;
use App\Transformers\TopicTransformer;
use Illuminate\Http\Request;

class TopicsController extends Controller
{
    /**
     * 首页话题列表
     * @param Request $request
     * @param Topic $topic
     * @return \Dingo\Api\Http\Response
     */
    public function index(Request $request, Topic $topic)
    {
        $query = $topic->query();

        if($categoryId = $request->category_id) {
            $query->where('category_id', $categoryId);
        }

        switch ($request->order) {
            case 'recent':
                // 最新发布排序
                $query->recent();
                break;
            default:
                // 最近回复排序
                $query->recentReplied();
                break;
        }

        $topics  = $query->paginate(20);

        return $this->response->paginator($topics, new TopicTransformer());
    }

    /**
     * @param User $user
     * @param Request $request
     * @return \Dingo\Api\Http\Response
     */
    public function userIndex(User $user, Request $request)
    {
        $topics = $user->topics()->recent()->paginate(20);

        return $this->response->paginator($topics, new TopicTransformer());
    }

    /**
     * 发布话题
     * @param TopicRequest $request
     * @param Topic $topic
     * @return \Dingo\Api\Http\Response
     */
    public function store(TopicRequest $request, Topic $topic)
    {
        $topic->fill($request->all());
        $topic->user_id = $this->user()->id;
        $topic->save();

        return $this->response->item($topic, new TopicTransformer())
            ->setStatusCode(201);
    }

    /**
     * 修改话题
     * @param TopicRequest $request
     * @param Topic $topic
     * @return \Dingo\Api\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function update(TopicRequest $request, Topic $topic)
    {
        $this->authorize('update', $topic);

        $topic->update($request->all());
        return $this->response->item($topic, new TopicTransformer());
    }

    /**
     * 删除话题
     * @param Topic $topic
     * @return \Dingo\Api\Http\Response
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function destroy(Topic $topic)
    {
        $this->authorize('destroy', $topic);

        $topic->delete();
        return $this->response->noContent();
    }
}