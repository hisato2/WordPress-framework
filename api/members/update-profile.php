<?php

declare(strict_types=1);

use HKS\Auth\Auth;
use HKS\Members\MemberService;

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
| CSRFチェック
|--------------------------------------------------------------------------
*/

$nonce = isset($_POST['hks_update_member_profile_nonce'])
    ? sanitize_text_field(
        wp_unslash($_POST['hks_update_member_profile_nonce'])
    )
    : '';

if (
    $nonce === ''
    || !wp_verify_nonce(
        $nonce,
        'hks_update_member_profile_action'
    )
) {
    wp_die(
        'セキュリティ確認に失敗しました。もう一度お試しください。',
        '認証エラー',
        ['response' => 403]
    );
}

/*
|--------------------------------------------------------------------------
| 会員ID取得
|--------------------------------------------------------------------------
*/

$userId = isset($_POST['user_id'])
    ? absint($_POST['user_id'])
    : 0;

if ($userId <= 0) {
    wp_die(
        '会員IDが正しくありません。',
        '入力エラー',
        ['response' => 400]
    );
}

/*
|--------------------------------------------------------------------------
| 入力データ取得
|--------------------------------------------------------------------------
*/

$memberData = [
    'last_name' => isset($_POST['last_name'])
        ? sanitize_text_field(
            wp_unslash($_POST['last_name'])
        )
        : '',

    'first_name' => isset($_POST['first_name'])
        ? sanitize_text_field(
            wp_unslash($_POST['first_name'])
        )
        : '',

    'email' => isset($_POST['email'])
        ? sanitize_email(
            wp_unslash($_POST['email'])
        )
        : '',

    'role' => isset($_POST['role'])
        ? sanitize_key(
            wp_unslash($_POST['role'])
        )
        : '',

    'status' => isset($_POST['status'])
        ? sanitize_key(
            wp_unslash($_POST['status'])
        )
        : '',
];

/*
|--------------------------------------------------------------------------
| セッション取得
|--------------------------------------------------------------------------
*/

$auth = new Auth();

/*
|--------------------------------------------------------------------------
| プロフィール画像アップロード
|--------------------------------------------------------------------------
*/

if (
    isset($_FILES['profile_image'])
    && is_array($_FILES['profile_image'])
    && isset($_FILES['profile_image']['error'])
    && (int) $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE
) {
    $avatarFile = dirname(__DIR__)
        . '/upload/avatar.php';

    if (!is_file($avatarFile)) {

        $auth->session()->flash(
            'error',
            '画像アップロード処理を読み込めませんでした。'
        );

        wp_safe_redirect(
            add_query_arg(
                [
                    'view' => 'user-detail',
                    'id'   => $userId,
                ],
                home_url('/dashboard/')
            )
        );

        exit;
    }

    require_once $avatarFile;

    $uploadResult = hks_upload_member_avatar(
        $_FILES['profile_image'],
        $userId
    );

    if (empty($uploadResult['success'])) {

        $message = isset($uploadResult['message'])
            && is_string($uploadResult['message'])
                ? $uploadResult['message']
                : 'プロフィール画像を保存できませんでした。';

        $auth->session()->flash(
            'error',
            $message
        );

        wp_safe_redirect(
            add_query_arg(
                [
                    'view' => 'user-detail',
                    'id'   => $userId,
                ],
                home_url('/dashboard/')
            )
        );

        exit;
    }

    if (
        isset($uploadResult['path'])
        && is_string($uploadResult['path'])
        && $uploadResult['path'] !== ''
    ) {
        $memberData['profile_image'] = $uploadResult['path'];
    }
}

/*
|--------------------------------------------------------------------------
| 会員情報更新
|--------------------------------------------------------------------------
*/

$service = new MemberService();

$result = $service->updateProfile(
    $userId,
    $memberData
);

/*
|--------------------------------------------------------------------------
| 更新成功
|--------------------------------------------------------------------------
*/

if (!empty($result['success'])) {

    $message = isset($result['message'])
        && is_string($result['message'])
            ? $result['message']
            : '会員情報を更新しました。';

    $auth->session()->flash(
        'success',
        $message
    );

    wp_safe_redirect(
        add_query_arg(
            [
                'view' => 'user-detail',
                'id'   => $userId,
            ],
            home_url('/dashboard/')
        )
    );

    exit;
}

/*
|--------------------------------------------------------------------------
| 更新失敗
|--------------------------------------------------------------------------
*/

$message = isset($result['message'])
    && is_string($result['message'])
        ? $result['message']
        : '会員情報を更新できませんでした。';

$auth->session()->flash(
    'error',
    $message
);

/*
|--------------------------------------------------------------------------
| 会員詳細画面へ戻る
|--------------------------------------------------------------------------
*/

wp_safe_redirect(
    add_query_arg(
        [
            'view' => 'user-detail',
            'id'   => $userId,
        ],
        home_url('/dashboard/')
    )
);

exit;