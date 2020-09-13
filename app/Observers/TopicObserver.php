<?php

namespace App\Observers;

use App\Jobs\TranslateSlug;
use App\Models\Topic;

// creating, created, updating, updated, saving,
// saved,  deleting, deleted, restoring, restored

class TopicObserver
{
    public function creating(Topic $topic)
    {
        //
    }

    public function updating(Topic $topic)
    {
        //
    }

    public function saving(Topic $topic)
    {
        // 采用 mews/purifier 对 body 内容进行 xss 过滤
        $topic->body = clean($topic->body, 'user_topic_body');
        // 截取body 的内容，作为excerpt的内容
        $topic->excerpt = make_excerpt($topic->body);
    }

    public function saved(Topic $topic)
    {
        // 如 slug 字段无内容，即使用翻译器对 title 进行翻译
        if (!$topic->slug || $topic->isDirty('title')) {
            // $topic->slug =  app(SlugTranslateHandler::class)->translate($topic->title);

            // 推送任务到队列，实现异步处理
            dispatch(new TranslateSlug($topic));
        }
    }
}
