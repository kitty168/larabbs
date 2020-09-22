<?php
/**
 *
 * ActingJWTUser.php.
 * User: kitty.cheng
 * Mail: 450038893@qq.com
 * Date: 2020/9/22
 * Time: 23:52
 */

namespace Tests\Traits;


use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait ActingJWTUser
{

    public function JWTActingAs(User $user)
    {
        $token = Auth::guard('api')->fromUser($user);
        $this->withHeaders(['Authorization' => 'Bearer '.$token]);

        return $this;
    }
}
