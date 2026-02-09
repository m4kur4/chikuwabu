<?php

namespace App\Http\Controllers;

use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminates\Support\Facades\Auth;

class GitHubAuthController extends Controller
{
    // GitHubの認証画面へリダイレクト
    public function redirect()
    {
        return Socialite::driver('github')
            ->scopes(['repo', 'read:user'])
            ->redirect();
    }
}
