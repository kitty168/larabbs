<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Api\AuthorizationsRquest;
use App\Http\Requests\Api\SocialAuthorizationRequest;
use App\Models\User;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Psr\Http\Message\ServerRequestInterface;
use Zend\Diactoros\Response as Psr7Response;

class AuthorizationsController extends Controller
{
    /**
     * 第三方认证授权
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

            // 得到用户的认证信息
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

        // 通过 模型实例 生成token
        $token = Auth::guard('api')->fromUser($user);

        return $this->respondWithToken($token);
    }

    /**
     * 用户登录，生成token
     * @param AuthorizationsRquest $request
     * @return \Dingo\Api\Http\Response|void
     */
    public function store(AuthorizationsRquest $request, AuthorizationServer $server, ServerRequestInterface $serverRequest)
    {
        // oauth2.0 passport 认证登录
        try {
            return $server->respondToAccessTokenRequest($serverRequest, new Psr7Response)->withStatus(201);
        }catch (OAuthServerException $e) {
            return $this->response->errorUnauthorized($e->getMessage());
        }


        // 默认得 auth 认证登录
        /*
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
        */

    }

    /**
     * 刷新token
     * @return \Dingo\Api\Http\Response
     */
    public function update(AuthorizationServer $server, ServerRequestInterface $serverRequest)
    {
        // 采用 oauth2.0 刷新token
        try {
            return $server->respondToAccessTokenRequest($serverRequest, new Psr7Response());
        } catch (OAuthServerException $exception) {
            return $this->response->errorUnauthorized($exception->getMessage());
        }

        // 普通模式的 Auth 方式刷新 token
        // $token = Auth::guard('api')->refresh();
        // return $this->respondWithToken($token);
    }

    /**
     * 删除token，可以理解为退出登录
     * @return \Dingo\Api\Http\Response
     */
    public function destroy()
    {
        // 采用 oauth2.0 删除token
        if(!empty($this->user())){
            // 退出登录，清除token
            $this->user()->token()->revoke();
            return $this->response->noContent();
        }else{
            return $this->response->errorUnauthorized('The token is invalid.');
        }

        // 普通模式的 Auth 方式刷新 token
        // Auth::guard('api')->logout();
        // return $this->response->noContent();
    }

    protected function respondWithToken($token)
    {
        return $this->response->array([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
        ]);
    }
}
