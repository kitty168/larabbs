<?php

namespace App\Models;

use Auth;
use Carbon\Carbon;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\MustVerifyEmail as MustVerifyEmailTrait;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Support\Str;
use Redis;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements MustVerifyEmailContract, JWTSubject
{
    use Traits\LastActivedAtHelper;
    use Traits\ActiveUserHelper;
    use HasRoles;
    use MustVerifyEmailTrait;

    use Notifiable {
        notify as protected laravelNotify;
    }

    public function notify($instance)
    {
        // 如果要通知的人是当前用户，就不必通知了
        if ($this->id == Auth::id()) {
            return ;
        }

        // 只有数据库类型的通知才需要提醒，直接发送 Email 或其他类型的通知都 pass
        if (method_exists($instance, 'toDatabase')) {
            $this->increment('notification_count',1);
        }

        $this->laravelNotify($instance);
    }

    /**
     * The attributes that are mass assignable.
     * 可以被批量赋值的属性，即可以写入的字段
     * @var array
     */
    protected $fillable = [
        'name', 'phone', 'email', 'password','avatar','introduction',
        'weixin_openid', 'weixin_unionid'
    ];

    /**
     * The attributes that should be hidden for arrays.
     * 输出时隐藏的字段
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * 属性访问器
     * @param $value
     * @return string
     */
    public function getAvatarAttribute($value)
    {
        $default_avatar = 'https://cdn.learnku.com/uploads/images/201709/20/1/PtDKbASVcz.png?imageView2/1/w/600/h/60';
        return $value ?: $default_avatar;
    }

    public function topics()
    {
        return $this->hasMany(Topic::class);
    }

    public function replies()
    {
        return $this->hasMany(Reply::class);
    }

    /**
     * 是否为作者
     * @param $model
     * @return bool
     */
    public function isAuthorOf($model)
    {
        return $this->id == $model->user_id;
    }

    // 设置所有通知消息为已读，并清空通知消息
    public function markAsRead()
    {
        $this->notification_count = 0;
        $this->save();
        $this->unreadNotifications->markAsRead();
    }

    public function setPasswordAttribute($value)
    {
        // 如果值的长度等于 60，即认为是已经做过加密的情况
        if (strlen($value) != 60) {

            // 不等于 60，做密码加密处理
            $value = bcrypt($value);
        }

        $this->attributes['password'] = $value;
    }

    public function setAvatarAttribute($path)
    {
        // 如果不是 `http` 子串开头，那就是从后台上传的，需要补全 URL
        if ( ! Str::startsWith($path, 'http')) {

            // 拼接完整的 URL
            $path = config('app.url') . "/uploads/images/avatars/$path";
        }

        $this->attributes['avatar'] = $path;
    }

    /**
     * @return mixed|void
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * @return array|void
     */
    public function getJWTCustomClaims()
    {
        return [];
    }



}
