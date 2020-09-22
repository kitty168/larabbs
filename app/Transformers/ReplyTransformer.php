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
    public function transform(Reply $reply)
    {
        return [
            'id' => $reply->id,
            'content' => $reply->content,
            'user_id' => (int) $reply->user_id,
            'topic_id' => (int) $reply->topic_id,
            'created_at' => (string) $reply->created_at,
            'updated_at' => (string) $reply->updated_at,
        ];
    }


}
