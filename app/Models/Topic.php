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

    /**
     * 排序分类
     *
     * ====================================================================
     * 这里我们使用了 Laravel 本地作用域 。
     * 本地作用域允许我们定义通用的约束集合以便在应用中复用。
     * 要定义这样的一个作用域，只需简单在对应 Eloquent 模型方法前加上一个 scope 前缀，
     * 作用域总是返回 查询构建器。一旦定义了作用域，则可以在查询模型时调用作用域方法。
     * 在进行方法调用时不需要加上 scope 前缀。如代码中的 recent() 和 recentReplied()。
     * ====================================================================
     *
     * @param $query
     * @param $order
     * @return mixed
     */
    public function scopeWithOrder($query, $order)
    {
        // 不同的排序，使用不同的数据读取逻辑
        switch ($order) {
            case 'recent':
                $query->recent();
                break;
            default:
                $query->recentReplied();
                break;
        }

        // 预加载防止 N+1 问题
        return $query->with('user', 'category');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeRecentReplied($query)
    {
        // 当话题有新回复时，我们将编写逻辑来更新话题模型的 reply_count 属性，
        // 此时会自动触发框架对数据模型 updated_at 时间戳的更新
        return $query->orderBy('updated_at', 'desc');
    }

    /**
     * @param $query
     * @return mixed
     */
    public function scopeRecent($query)
    {
        // 按照创建时间排序
        return $query->orderBy('created_at', 'desc');
    }

}
