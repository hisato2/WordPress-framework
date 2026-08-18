<?php

declare(strict_types=1);

use HKS\Auth\Auth;

defined('ABSPATH') || exit;


/*
|--------------------------------------------------------------------------
| POST確認
|--------------------------------------------------------------------------
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
| 管理画面アクセス権限確認
|--------------------------------------------------------------------------
|
| ログイン済みであっても、
| 管理画面へアクセスできないユーザーには
| 会員パスワードを変更させない。
|
*/

if (!$auth->canAccessAdmin()) {
    wp_safe_redirect(
        home_url('/mypage/')
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| CSRFチェック
|--------------------------------------------------------------------------
*/

$nonce = isset($_POST['hks_admin_change_member_password_nonce'])
    ? sanitize_text_field(
        wp_unslash(
            $_POST['hks_admin_change_member_password_nonce']
        )
    )
    : '';


if (
    $nonce === ''
    || !wp_verify_nonce(
        $nonce,
        'hks_admin_change_member_password_action'
    )
) {
    $auth->session()->flash(
        'error',
        'セキュリティ確認に失敗しました。もう一度お試しください。'
    );


    wp_safe_redirect(
        home_url('/dashboard/?view=users')
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| 対象会員ID取得
|--------------------------------------------------------------------------
*/

$userId = isset($_POST['user_id'])
    ? absint($_POST['user_id'])
    : 0;


if ($userId <= 0) {
    $auth->session()->flash(
        'error',
        '対象会員を確認できませんでした。'
    );


    wp_safe_redirect(
        home_url('/dashboard/?view=users')
    );

    exit;
}


/*
|--------------------------------------------------------------------------
| 戻り先URL
|--------------------------------------------------------------------------
*/

$passwordUrl = add_query_arg(
    [
        'view' => 'user-password',
        'id'   => $userId,
    ],
    home_url('/dashboard/')
);


$detailUrl = add_query_arg(
    [
        'view' => 'user-detail',
        'id'   => $userId,
    ],
    home_url('/dashboard/')
);


/*
|--------------------------------------------------------------------------
| パスワード取得
|--------------------------------------------------------------------------
|
| パスワードには sanitize_text_field() を使用しない。
| 記号等を変換しないため。
|
*/

$newPassword = isset($_POST['new_password'])
    ? (string) wp_unslash($_POST['new_password'])
    : '';


$newPasswordConfirmation =
    isset($_POST['new_password_confirmation'])
        ? (string) wp_unslash(
            $_POST['new_password_confirmation']
        )
        : '';


/*
|--------------------------------------------------------------------------
| 管理者による会員パスワード変更
|--------------------------------------------------------------------------
*/

$result = $auth->changeMemberPassword(
    $userId,
    [
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


    wp_safe_redirect($passwordUrl);

    exit;
}


/*
|--------------------------------------------------------------------------
| 変更成功
|--------------------------------------------------------------------------
*/

$auth->session()->flash(
    'success',
    '会員のパスワードを変更しました。'
);


wp_safe_redirect($detailUrl);

exit;