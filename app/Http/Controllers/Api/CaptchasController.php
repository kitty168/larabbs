<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\CaptchaRequest;
use Gregwar\Captcha\CaptchaBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CaptchasController extends Controller
{
    public function store(CaptchaRequest $request, CaptchaBuilder $captchaBuilder)
    {
        $key = 'captcha-'.Str::random(15);

        $phone = $request->phone;

        // 创建验证码图片
        $captcha = $captchaBuilder->build();

        $expiredAt = now()->addMinutes(2);

        // $captcha->getPhrase() 获取验证码的文本
        Cache::put($key, ['phone' => $phone, 'code' => $captcha->getPhrase()], $expiredAt);

        $result = [
            'captcha_key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
            'captcha_image_content' => $captcha->inline(), // base64的图片验证码
        ];

        return $this->response->array($result)->setStatusCode(201);

    }
}
