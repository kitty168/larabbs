<?php
/**
 *
 * ReplyTransformer.php.
 * User: kitty.cheng
 * Mail: 450038893@qq.com
 * Date: 2020/9/21
 * Time: 16:07
 */

namespace App\Transformers;


use App\Models\Reply;
use League\Fractal\TransformerAbstract;

class ReplyTransformer extends TransformerAbstract
{
    protected $availableIncludes = ['user','topic'];

    public function transform(Reply $reply)
    {
        return [
            'id' => $reply->id,
            'content' => $reply->content,
            'user_id' => $reply->user_id,
            'topic_id' => $reply->topic_id,
            'created_at' => (string) $reply->created_at,
            'updated_at' => (string) $reply->updated_at,
        ];
    }

    public function includeUser(Reply $reply)
    {
        return $this->item($reply->user, new UserTransformer());
    }

    public function includeTopic(Reply $reply)
    {
        return $this->item($reply->topic, new TopicTransformer());
    }


}
