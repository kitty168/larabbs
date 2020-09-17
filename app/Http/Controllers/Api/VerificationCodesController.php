<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\VerificationCodeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Overtrue\EasySms\EasySms;
use Overtrue\EasySms\Exceptions\NoGatewayAvailableException;

class VerificationCodesController extends Controller
{
    /**
     * @param VerificationCodeRequest $request
     * @param EasySms $easySms
     * @return \Dingo\Api\Http\Response
     * @throws \Exception
     */
    public function store(VerificationCodeRequest $request, EasySms $easySms)
    {
        $phone = $request->phone;

        if (! app()->environment('production')) {
            // 非生产环境下，指定 code 为 1234 方便测试
            $code = '1234';
        } else {
            // 生成4位的随机数，作为验证码
            $code = str_pad(random_int(1,9999), 4, 0, STR_PAD_LEFT);

            try {
                // 调用 EasySms 发送短信
                $result = $easySms->send($phone, [
                    'template' => 'SMS_127805181',
                    'data' => [
                        'code' => $code
                    ]
                ]);
            }catch (NoGatewayAvailableException $exception) {
                $message = $exception->getException('aliyun')->getMessage();
                return $this->response->errorInternal($message ?: '短信发送异常');
            }
        }


        // 用户缓存的键名
        $key = 'verificationCode_'.Str::random(15);
        // 缓存过期时间，当前时间往后 10 分钟
        $expiredAt = now()->addMinutes(10);

        Cache::put($key, ['phone' => $phone, 'code' => $code], $expiredAt);

        return $this->response->array([
            'key' => $key,
            'expired_at' => $expiredAt->toDateTimeString(),
        ])->setStatusCode(201);
    }
}
