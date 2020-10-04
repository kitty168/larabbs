<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AuthorizationsRquest;
use App\Http\Requests\Api\SocialAuthorizationRequest;
use App\Http\Requests\Api\WeappAuthorizationRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class AuthorizationsController extends Controller
{
    /**
     * 第三方登录
     * @param $type
     * @param SocialAuthorizationRequest $request
     * @return \Dingo\Api\Http\Response|void
     */
    public function socialStore($type, SocialAuthorizationRequest $request)
    {
        if(!in_array($type, ['weixin'])){
            // http code 400
            return $this->response->errorBadRequest();
        }

        $driver = Socialite::driver($type);

        try{
            if($code = $request->code){
                $response = $driver->getAccessTokenResponse($code);
                $token = Arr::get($response, 'access_token');
            } else {
                $token = $request->access_token;

                if($type == 'weixin'){
                    $driver->setOpenId($request->openid);
                }
            }

            // 等到用户的认证信息
            $oauthUser = $driver->userFromToken($token);

        }catch (\Exception $e){
            // 401 Unauthorized
            return $this->response->errorUnauthorized('参数错误，未获取用户信息');
        }

        switch ($type) {
            case 'weixin':
                $unionid = $oauthUser->offsetExists('unionid') ? $oauthUser->offsetGet('unionid') : null;

                if($unionid){
                    $user = User::where('weixin_unionid', $unionid)->first();
                } else {
                    $user = User::where('weixin_openid', $oauthUser->getId())->first();
                }

                // 用户不存在，就创建一个
                if (!$user) {
                    $user = User::create([
                        'name' => $oauthUser->getNickname(),
                        'avatar' => $oauthUser->getAvatar(),
                        'weixin_openid' => $oauthUser->getId(),
                        'weixin_unionid' => $unionid,
                    ]);
                }

                break;

        }

        $token = Auth::guard('api')->fromUser($user);

        return $this->respondWithToken($token);
    }

    /**
     * 登录
     * @param AuthorizationsRquest $request
     * @return \Dingo\Api\Http\Response|void
     */
    public function store(AuthorizationsRquest $request)
    {
        $username = $request->username;

        filter_var($username, FILTER_VALIDATE_EMAIL) ?
            $credentials['email'] = $username :
            $credentials['phone'] = $username;

        $credentials['password'] = $request->password;

        // attempt 方法可以根据参数查找数据库里是否存在该用户，存在则生成token
        if (!$token = Auth::guard('api')->attempt($credentials)) {
            // return $this->response->errorUnauthorized('用户名或密码错误');
            // 本地化语言设置
            return $this->response->errorUnauthorized(trans('auth.failed'));
        }

        return $this->respondWithToken($token);

    }

    /**
     * 小程序登录
     * @param WeappAuthorizationRequest $request
     * @return \Dingo\Api\Http\Response|void
     */
    public function weappStore(WeappAuthorizationRequest $request)
    {
        $code = $request->code;

        // 根据 code 获取微信 openid 和 session_key
        $miniProgram = \EasyWeChat::miniProgram();

        $data = $miniProgram->auth->session($code);

        // 如果结果错误，说明 code 已过期或不正确，返回 401 错误
        if (isset($data['errcode'])) {
            return $this->response->errorUnauthorized('code 不正确');
        }

        // 找到 openid 对应的用户
        $user = User::where('weapp_openid', $data['openid'])->first();

        $attributes['weixin_session_key'] = $data['session_key'];

        // 没有找到对应用户则需要提交用户名和密码进行用户绑定
        if (!$user) {
            // 如果未提交用户名，返回403错误
            if (!$request->username) {
                return $this->response->errorForbidden('用户不存在');
            }
            $username = $request->username;

            // 用户名可以是邮箱或手机号
            filter_var($username, FILTER_VALIDATE_EMAIL) ?
                $credentials['email'] = $username :
                $credentials['phone'] = $username;

            $credentials['password'] = $request->password;

            // 验证用户名和密码是否正确
            if(!Auth::guard('api')->once($credentials)) {
                return $this->response->errorUnauthorized('用户名或密码错误');
            }

            // 获取对应的用户
            $user = Auth::guard('api')->getUser();
            $attributes['weapp_openid'] = $data['openid'];

        }

        // 更新用户数据
        $user->update($attributes);

        // 为对用用户创建 JWT
        $token = Auth::guard('api')->fromUser($user);

        return $this->respondWithToken($token)->setStatusCode(201);

    }



    /**
     * 刷新token
     * @return \Dingo\Api\Http\Response
     */
    public function update()
    {
        $token = Auth::guard('api')->refresh();
        return $this->respondWithToken($token);
    }

    /**
     * 删除token，可以理解为退出登录
     * @return \Dingo\Api\Http\Response
     */
    public function destroy()
    {
        Auth::guard('api')->logout();
        return $this->response->noContent();
    }

    /**
     * 携带token进行返回
     * @param $token
     * @return \Dingo\Api\Http\Response
     */
    protected function respondWithToken($token)
    {
        return $this->response->array([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
        ]);
    }
}
