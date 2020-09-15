<?php
/**
 *
 * LastActivedAtHelper.php.
 * User: kitty.cheng
 * Mail: 450038893@qq.com
 * Date: 2020/9/15
 * Time: 17:23
 */

namespace App\Models\Traits;


use Carbon\Carbon;
use Redis;


trait LastActivedAtHelper
{
    // 缓存相关
    protected $hash_prefix = 'larabbs_last_actived_at_';
    protected $field_prefix = 'user_';

    public function recordLastActivedAt()
    {
        // 获取今天的日期
        // $date = Carbon::now()->toDateString();

        // Redis 哈希表的命名， 如： larabbs_last_actived_at_2020-09-15
        $hash = $this->getHashFromDateString(Carbon::now()->toDateString());

        // 字段名称， 如： user_1
        $field = $this->getHashField();

        // 当前时间， 如： 2020-09-15 17:35:21
        $now = Carbon::now()->toDateTimeString();

        // 数据写入 Redis , 字段已存在会被更新
        Redis::hSet($hash, $field, $now);

    }

    public function syncUserActivedAt()
    {
        // 获取昨天的日期， 格式如： 2020-09-14
        // $yesterday_date = Carbon::yesterday()->toDateString();

        // Redis 哈希表的命名，如： larabbs_last_actived_at_2020-09-15
        $hash = $this->getHashFromDateString(Carbon::yesterday()->toDateString());

        // 从 Redis 中获取所有哈希表里的数据
        $datas = Redis::hGetAll($hash);

        // 遍历数据
        foreach ($datas as $user_id => $actived_at) {
            $user_id = str_replace($this->field_prefix, '', $user_id);

            // 只有当用户存在时才更新到数据库中
            if ($user = $this->find($user_id)) {
                $user->timestamps = false;
                $user->last_actived_at = $actived_at;
                $user->save();
            }
        }

        Redis::del($hash);
    }

    public function getLastActivedAtAttribute($value)
    {
        $hash = $this->getHashFromDateString(Carbon::now()->toDateString());

        $field = $this->getHashField();

        $datetime = Redis::hGet($hash, $field) ? : $value;

        if ($datetime) {
            return new Carbon($datetime);
        } else {
            return $this->created_at;
        }
    }

    protected function getHashFromDateString($date_string)
    {
        return $this->hash_prefix . $date_string;
    }

    protected function getHashField()
    {
        return $this->field_prefix . $this->id;
    }
}
