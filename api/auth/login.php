<?php

declare(strict_types=1);

use HKS\Auth\Auth;

if (!defined('ABSPATH')) {
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    wp_die(
        '不正なアクセスです。',
        'アクセスエラー',
        ['response' => 405]
    );
}

/*
|--------------------------------------------------------------------------
| CSRFチェック
|--------------------------------------------------------------------------
*/

$nonce = isset($_POST['hks_login_nonce'])
    ? sanitize_text_field(wp_unslash($_POST['hks_login_nonce']))
    : '';

if (
    empty($nonce) ||
    !wp_verify_nonce($nonce, 'hks_login_action')
) {
    wp_die(
        'セキュリティ確認に失敗しました。もう一度お試しください。',
        '認証エラー',
        ['response' => 403]
    );
}

/*
|--------------------------------------------------------------------------
| ログイン処理
|--------------------------------------------------------------------------
*/

$auth = new Auth();

$loginData = [
    'email' => isset($_POST['email'])
        ? sanitize_email(wp_unslash($_POST['email']))
        : '',

    'password' => isset($_POST['password'])
        ? (string) wp_unslash($_POST['password'])
        : '',
];

$result = $auth->login($loginData);

/*
|--------------------------------------------------------------------------
| ログイン成功
|--------------------------------------------------------------------------
*/

if (!empty($result['success'])) {
    wp_safe_redirect(home_url('/mypage/'));
    exit;
}

/*
|--------------------------------------------------------------------------
| ログイン失敗
|--------------------------------------------------------------------------
*/

$message = isset($result['message']) && is_string($result['message'])
    ? $result['message']
    : 'ログインに失敗しました。';

/*
|--------------------------------------------------------------------------
| Flashメッセージ保存
|--------------------------------------------------------------------------
*/

$auth->session()->flash(
    'login_error',
    $message
);

$auth->session()->flash(
    'login_old_email',
    $loginData['email']
);

/*
|--------------------------------------------------------------------------
| ログイン画面へ戻る
|--------------------------------------------------------------------------
*/

wp_safe_redirect(home_url('/login/'));
exit;