<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Dingo/Api 接管路由
$api = app(\Dingo\Api\Routing\Router::class);

$api->version('v1', [
    'namespace' => 'App\Http\Controllers\Api'
], function($api) {

    //访问频率控制分组
    $api->group([
        // 访问频率中间件
        // 1分钟调用1次
        'middleware' => 'api.throttle',
        // 调用次数
        'limit' => config('api.rate_limits.sign.limit'),
        // 周期 单位：分钟
        'expires' => config('api.rate_limits.sign.expires'),
    ],function($api){
        // 发送验证码
        $api->post('verificationCodes', 'VerificationCodesController@store')
            ->name('api.verificationCodes.store');

        // 用户注册
        $api->post('users', 'UsersController@store')
            ->name('api.users.store');
    });


});

// 不同版本的 api 接口
$api->version('v2', function($api) {
    $api->get('version',function (){
        return response('this is version v2');
    });
});
