<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

/**
 * 表单验证器
 * Class UserRequest
 * @package App\Http\Requests
 */
class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * 权限验证
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     * 表单验证规则
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|between:3,25|regex:/^[A-Aa-z0-9\-\_]+$/|unique:users,name,'.Auth::id(),
            'email' => 'required|email',
            'introduction' => 'max:80',
        ];
    }

    public function messages()
    {
        return [
            'name.between' => '用户名必须介于 3 - 25 个字符之间。',
            'name.regex' => '用户名只支持英文、数字、横杠和下划线。',
            'name.required' => '用户名不能为空。',
            'name.unique' => '用户名已被占用，请重新填写。',
        ];
    }


}
