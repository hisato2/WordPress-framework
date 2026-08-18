<?php


declare(strict_types=1);


use HKS\Auth\Auth;


defined('ABSPATH') || exit;


/*
|--------------------------------------------------------------------------
| POST確認
|--------------------------------------------------------------------------
|
| このAPIは /profile/ からPOSTされた場合のみ使用する。
|
*/


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    wp_die(
        '不正なアクセスです。',
        'アクセスエラー',
        ['response' => 405]
    );
}


/*
|--------------------------------------------------------------------------
| 認証確認
|--------------------------------------------------------------------------
*/


$auth = new Auth();


if (!$auth->check()) {
    wp_safe_redirect(
        home_url('/login/')
    );


    exit;
}


/*
|--------------------------------------------------------------------------
| CSRFチェック
|--------------------------------------------------------------------------
*/


$nonce = isset($_POST['hks_change_password_nonce'])
    ? sanitize_text_field(
        wp_unslash($_POST['hks_change_password_nonce'])
    )
    : '';


if (
    $nonce === ''
    || !wp_verify_nonce(
        $nonce,
        'hks_change_password_action'
    )
) {
    $auth->session()->flash(
        'error',
        'セキュリティ確認に失敗しました。もう一度お試しください。'
    );


    wp_safe_redirect(
        home_url('/profile/?password=change')
    );


    exit;
}


/*
|--------------------------------------------------------------------------
| パスワード取得
|--------------------------------------------------------------------------
|
| パスワードには sanitize_text_field() を使用しない。
|
| パスワードに使用されている記号や空白などを
| 意図せず変更しないため、wp_unslash() のみ行う。
|
*/


$currentPassword = isset($_POST['current_password'])
    ? (string) wp_unslash(
        $_POST['current_password']
    )
    : '';


$newPassword = isset($_POST['new_password'])
    ? (string) wp_unslash(
        $_POST['new_password']
    )
    : '';


$newPasswordConfirmation =
    isset($_POST['new_password_confirmation'])
        ? (string) wp_unslash(
            $_POST['new_password_confirmation']
        )
        : '';


/*
|--------------------------------------------------------------------------
| パスワード変更
|--------------------------------------------------------------------------
|
| ログイン中ユーザーのID取得、
| 現在パスワード確認、
| 新パスワード検証、
| password_hash()、
| DB更新はAuth / PasswordService側で処理する。
|
*/


$result = $auth->changePassword(
    [
        'current_password' => $currentPassword,
        'new_password' => $newPassword,
        'new_password_confirmation' => $newPasswordConfirmation,
    ]
);


/*
|--------------------------------------------------------------------------
| 変更失敗
|--------------------------------------------------------------------------
*/


if (empty($result['success'])) {
    $auth->session()->flash(
        'error',
        isset($result['message'])
            ? (string) $result['message']
            : 'パスワードを変更できませんでした。'
    );


    /*
     * ?password=change を付けて戻すことで、
     * profile.js がパスワード変更フォームを再表示する。
     */
    wp_safe_redirect(
        home_url('/profile/?password=change')
    );


    exit;
}


/*
|--------------------------------------------------------------------------
| 変更成功
|--------------------------------------------------------------------------
*/


$auth->session()->flash(
    'success',
    isset($result['message'])
        ? (string) $result['message']
        : 'パスワードを変更しました。'
);


/*
|--------------------------------------------------------------------------
| Profileへ戻る
|--------------------------------------------------------------------------
*/


wp_safe_redirect(
    home_url('/profile/')
);


exit;