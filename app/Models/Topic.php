<?php

namespace App\Models;

class Topic extends Model
{
    /*
        title => 帖子标题
        body => 帖子内容
        user_id => 用户 ID
        category_id => 分类 ID
        reply_count => 回复数量
        view_count => 查看总数
        last_reply_user_id => 最后回复的用户 ID
        order => 可用来做排序使用
        excerpt => 文章摘要，SEO 优化时使用
        slug => SEO 友好的 URI
    */
    protected $fillable = [
        'title', 'body', 'user_id', 'category_id', 'reply_count',
        'view_count', 'last_reply_user_id', 'order', 'excerpt', 'slug'
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
