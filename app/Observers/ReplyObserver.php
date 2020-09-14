<?php

namespace App\Observers;

use App\Models\Reply;
use App\Notifications\TopicReplied;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class ReplyObserver
{
    public function creating(Reply $reply)
    {
        $reply->content = clean($reply->content,'user_topic_body');
    }

    public function created(Reply $reply)
    {
        // 命令行运行迁移时不做这些操作
        if ( ! app()->runningInConsole()) {
            // 评论数自增1
            // $reply->topic->increment('reply_count', 1);
            // 逻辑严谨调优
            // $reply->topic->reply_count = $reply->topic->replies->count();
            // $reply->topic->save();
            // 再次优化封装
            $reply->topic->updateReplyCount();

            // 通知话题作者有行的评论
            $reply->topic->user->notify(new TopicReplied($reply));
        }

    }

    public function updating(Reply $reply)
    {
        //
    }

    public function deleted(Reply $reply)
    {
        // $reply->topic->reply_count = $reply->topic->replies->count();
        // $reply->topic->save();

        // 优化封装
        $reply->topic->updateReplyCount();
    }
}
