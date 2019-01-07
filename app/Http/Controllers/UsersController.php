<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class UsersController extends Controller
{
    /**
     * [show description]
     * @param  User   $user [description]
     * @return [type]       [description]
     */
    public function show(User $user)
    {
        return view('users.show', compact('user'));
    }
}