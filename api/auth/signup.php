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

$nonce = isset($_POST['hks_signup_nonce'])
    ? sanitize_text_field(wp_unslash($_POST['hks_signup_nonce']))
    : '';

if (
    empty($nonce) ||
    !wp_verify_nonce($nonce, 'hks_signup_action')
) {
    wp_die(
        'セキュリティ確認に失敗しました。もう一度お試しください。',
        '認証エラー',
        ['response' => 403]
    );
}

/*
|--------------------------------------------------------------------------
| 入力データ
|--------------------------------------------------------------------------
*/

$signupData = [

    'last_name' => isset($_POST['last_name'])
        ? sanitize_text_field(wp_unslash($_POST['last_name']))
        : '',

    'first_name' => isset($_POST['first_name'])
        ? sanitize_text_field(wp_unslash($_POST['first_name']))
        : '',

    'email' => isset($_POST['email'])
        ? sanitize_email(wp_unslash($_POST['email']))
        : '',

    'password' => isset($_POST['password'])
        ? (string) wp_unslash($_POST['password'])
        : '',

    'password_confirmation' => isset($_POST['password_confirmation'])
        ? (string) wp_unslash($_POST['password_confirmation'])
        : '',

    'agree' => !empty($_POST['agree'])

];

$auth = new Auth();

$result = $auth->register($signupData);

/*
|--------------------------------------------------------------------------
| 登録成功
|--------------------------------------------------------------------------
*/

if (!empty($result['success'])) {

    $auth->session()->flash(
        'success',
        '会員登録が完了しました。ログインしてください。'
    );

    wp_safe_redirect(home_url('/login/'));
    exit;
}

/*
|--------------------------------------------------------------------------
| 登録失敗
|--------------------------------------------------------------------------
*/

$message = isset($result['message']) && is_string($result['message'])
    ? $result['message']
    : '会員登録に失敗しました。';

$auth->session()->flash(
    'signup_error',
    $message
);

$auth->session()->flash(
    'signup_old',
    [
        'last_name' => $signupData['last_name'],
        'first_name' => $signupData['first_name'],
        'email' => $signupData['email'],
        'agree' => $signupData['agree'],
    ]
);

wp_safe_redirect(home_url('/signup/'));
exit;