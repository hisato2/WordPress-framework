<?php

namespace HKS\Middleware;

use HKS\Auth\Auth;

class AuthMiddleware
{
    /**
     * ログイン必須
     */
    public static function handle(): void
    {
        $auth = new Auth();

        if (!$auth->check()) {

            wp_redirect(home_url('/login/'));
            exit;

        }
    }

    /**
     * ゲストのみ
     */
    public static function guest(): void
    {
        $auth = new Auth();

        if ($auth->check()) {

            wp_redirect(home_url('/mypage/'));
            exit;

        }
    }
}