<?php
/**
 * 处理slug seo link
 * 接入百度翻译api
 *
 * SlugTranslateHandler.php.
 * User: kitty.cheng
 * Mail: 450038893@qq.com
 * Date: 2020/9/13
 * Time: 16:05
 */

namespace App\Handlers;


use GuzzleHttp\Client;
use Overtrue\Pinyin\Pinyin;

class SlugTranslateHandler
{

    public function translate($text)
    {
        // 初始化配置信息
        $api = 'http://api.fanyi.baidu.com/api/trans/vip/translate?';
        $appid = config('services.baidu_translate.appid');
        $key = config('services.baidu_translate.key');
        $salt = time();

        // 如果没有配置百度翻译，自动使用兼容的拼音方案
        if (empty($appid) || empty($key)) {
            return $this->pinyin($text);
        }

        // 实例化 HTTP 客户端
        $http = new Client();

        // 根据文档，生成 sign
        // http://api.fanyi.baidu.com/api/trans/product/apidoc
        // appid+q+salt+密钥 的MD5值
        $sign = md5($appid . $text . $salt . $key);

        $query = http_build_query([
            'q'     => $text,
            'from'  => 'zh',
            'to'    => 'en',
            'appid' => $appid,
            'salt'  => $salt,
            'sign'  => $sign,
        ]);

        // 发送 HTTP Get 请求
        $response = $http->get($api.$query);

        $result = json_decode($response->getBody(), true);

        /*
         * 返回示例如下
        {
            "from": "zh",
            "to": "en",
            "trans_result": [
                {
                    "src": "中国",
                    "dst": "China"
                }
            ]
        }
         */

        // 尝试获取翻译结果
        if (isset($result['trans_result'][0]['dst'])) {
            return \Str::slug($result['trans_result'][0]['dst']);
        } else {
            // 兼容拼音
            return $this->pinyin($text);
        }
    }

    public function pinyin($text)
    {
        return \Str::slug(app(Pinyin::class)->permalink($text));
    }
}
