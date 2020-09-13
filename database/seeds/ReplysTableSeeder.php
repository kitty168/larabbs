<?php

use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Reply;

class ReplysTableSeeder extends Seeder
{
    public function run()
    {
        // 所有用户 ID 数组
        $user_ids = User::all()->pluck('id')->toArray();

        // 所有话题的 ID 数组
        $topic_ids = Topic::all()->pluck('id')->toArray();

        // 获取 Faker 实例
        $faker = app(\Faker\Generator::class);

        $replys = factory(Reply::class)
            ->times(200)
            ->make()
            ->each(function ($reply, $index)
                use ($user_ids, $topic_ids, $faker)
        {
            // 随机取一个用户 ID
            $reply->user_id = $faker->randomElement($user_ids);

            // 话题，随机取一个
            $reply->topic_id = $faker->randomElement($topic_ids);

        });

        // 将数据集合转换为数组，并插入到数据库中
        Reply::insert($replys->toArray());
    }

}

