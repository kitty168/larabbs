<?php
/**
 *
 * NotificationTransformer.php.
 * User: kitty.cheng
 * Mail: 450038893@qq.com
 * Date: 2020/9/21
 * Time: 16:07
 */

namespace App\Transformers;


use Illuminate\Notifications\DatabaseNotification;
use League\Fractal\TransformerAbstract;

class NotificationTransformer extends TransformerAbstract
{
    public function transform(DatabaseNotification $notification)
    {
        return [
            'id' => $notification->id,
            'type' => $notification->type,
            // 'notifiable' => $notification->notifiable,
            'data' => $notification->data,
            'read_at' => $notification->read_at ? $notification->read_at->toDateTimeString() : null,
            'created_at' => (string) $notification->created_at,
            'updated_at' => (string) $notification->updated_at,
        ];
    }

}
