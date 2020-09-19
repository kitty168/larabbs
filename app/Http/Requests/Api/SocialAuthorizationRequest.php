<?php

namespace App\Http\Requests\Api;

class SocialAuthorizationRequest extends FormRequest
{
    /**
     * @return array
     */
    public function rules()
    {
        $rules = [
            'code' => 'required_without:access_toke|string',
            'access_toke' => 'required_without:code|string',
        ];

        // 当第三方登录为 weixin 的时候，并且 code 不存在的时候，必传 openid
        if ($this->social_type == 'weixin' && !$this->code) {
            $rules['openid'] = 'required|string';
        }

        return $rules;
    }
}
