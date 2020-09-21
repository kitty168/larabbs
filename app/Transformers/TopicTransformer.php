<?php
/**
 *
 * TopicTransformer.php.
 * User: kitty.cheng
 * Mail: 450038893@qq.com
 * Date: 2020/9/21
 * Time: 16:07
 */

namespace App\Transformers;


use App\Models\Topic;
use League\Fractal\TransformerAbstract;

class TopicTransformer extends TransformerAbstract
{
    // 指定可以获取的额外资源,客户端可以通过其值作为参数名include的值传递，多个值用逗号隔开
    // 并创建对应的方法，命名为 include + User
    protected $availableIncludes = ['user', 'category'];

    public function transform(Topic $topic)
    {
        return [
            'id' => $topic->id,
            'title' => $topic->title,
            'body' => $topic->body,
            'user_id' => (int) $topic->user_id,
            'category_id' => (int) $topic->category_id,
            'reply_count' => (int) $topic->reply_count,
            'view_count' => (int) $topic->view_count,
            'last_reply_user_id' => (int) $topic->last_reply_user_id,
            'excerpt' => $topic->excerpt,
            'slug' => $topic->slug,
            'created_at' => (string) $topic->created_at,
            'updated_at' => (string) $topic->updated_at,
        ];
    }

    /**
     * @param Topic $topic
     * @return \League\Fractal\Resource\Item
     */
    public function includeUser(Topic $topic)
    {
        return $this->item($topic->user, new UserTransformer());
    }

    /**
     * @param Topic $topic
     * @return \League\Fractal\Resource\Item
     */
    public function includeCategory(Topic $topic)
    {
        return $this->item($topic->category, new CategoryTransformer());
    }

}
