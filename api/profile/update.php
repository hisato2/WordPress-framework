<?php


declare(strict_types=1);


use HKS\Auth\Auth;
use HKS\Auth\ProfileService;


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


$user = $auth->user();


if (
    empty($user)
    || empty($user['id'])
) {
    wp_safe_redirect(
        home_url('/login/')
    );
    exit;
}


$userId = (int) $user['id'];


/*
|--------------------------------------------------------------------------
| CSRFチェック
|--------------------------------------------------------------------------
*/


$nonce = isset($_POST['hks_update_profile_nonce'])
    ? sanitize_text_field(
        wp_unslash($_POST['hks_update_profile_nonce'])
    )
    : '';


if (
    $nonce === ''
    || !wp_verify_nonce(
        $nonce,
        'hks_update_profile_action'
    )
) {
    $auth->session()->flash(
        'error',
        'セキュリティ確認に失敗しました。もう一度お試しください。'
    );


    wp_safe_redirect(
        home_url('/profile/')
    );


    exit;
}


/*
|--------------------------------------------------------------------------
| 入力データ取得
|--------------------------------------------------------------------------
*/


$data = [
    'email' => isset($_POST['email'])
        ? sanitize_email(
            wp_unslash($_POST['email'])
        )
        : '',


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


    'last_name_kana' => isset($_POST['last_name_kana'])
        ? sanitize_text_field(
            wp_unslash($_POST['last_name_kana'])
        )
        : '',


    'first_name_kana' => isset($_POST['first_name_kana'])
        ? sanitize_text_field(
            wp_unslash($_POST['first_name_kana'])
        )
        : '',


    'phone' => isset($_POST['phone'])
        ? sanitize_text_field(
            wp_unslash($_POST['phone'])
        )
        : '',


    'postal_code' => isset($_POST['postal_code'])
        ? sanitize_text_field(
            wp_unslash($_POST['postal_code'])
        )
        : '',


    'prefecture' => isset($_POST['prefecture'])
        ? sanitize_text_field(
            wp_unslash($_POST['prefecture'])
        )
        : '',


    'city' => isset($_POST['city'])
        ? sanitize_text_field(
            wp_unslash($_POST['city'])
        )
        : '',


    'address1' => isset($_POST['address1'])
        ? sanitize_text_field(
            wp_unslash($_POST['address1'])
        )
        : '',


    'address2' => isset($_POST['address2'])
        ? sanitize_text_field(
            wp_unslash($_POST['address2'])
        )
        : '',


    'birthday' => isset($_POST['birthday'])
        ? sanitize_text_field(
            wp_unslash($_POST['birthday'])
        )
        : '',
];


/*
|--------------------------------------------------------------------------
| プロフィール基本情報更新
|--------------------------------------------------------------------------
*/


$service = new ProfileService();


$result = $service->update(
    $data
);


if (empty($result['success'])) {

    $message = isset($result['message'])
        && is_string($result['message'])
            ? $result['message']
            : 'プロフィールを更新できませんでした。';


    $auth->session()->flash(
        'error',
        $message
    );


    wp_safe_redirect(
        home_url('/profile/')
    );


    exit;
}


/*
|--------------------------------------------------------------------------
| プロフィール画像
|--------------------------------------------------------------------------
|
| 画像が選択されている場合のみアップロードする。
|
*/


if (
    isset($_FILES['profile_image'])
    && is_array($_FILES['profile_image'])
    && isset($_FILES['profile_image']['error'])
    && (int) $_FILES['profile_image']['error'] !== UPLOAD_ERR_NO_FILE
) {

    /*
    |--------------------------------------------------------------------------
    | 既存アップロード処理読み込み
    |--------------------------------------------------------------------------
    */


    $avatarFile = dirname(__DIR__)
        . '/upload/avatar.php';


    if (!is_file($avatarFile)) {

        $auth->session()->flash(
            'error',
            '基本情報は更新しましたが、画像アップロード処理を読み込めませんでした。'
        );


        wp_safe_redirect(
            home_url('/profile/')
        );


        exit;
    }


    require_once $avatarFile;


    /*
    |--------------------------------------------------------------------------
    | 画像アップロード
    |--------------------------------------------------------------------------
    */


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
            '基本情報は更新しましたが、'
            . $message
        );


        wp_safe_redirect(
            home_url('/profile/')
        );


        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | 画像パスをDBへ保存
    |--------------------------------------------------------------------------
    */


    $imagePath = isset($uploadResult['path'])
        && is_string($uploadResult['path'])
            ? $uploadResult['path']
            : '';


    if ($imagePath !== '') {

        $imageUpdated = $service->updateProfileImage(
            $imagePath
        );


        if (empty($imageUpdated['success'])) {

            $message = isset($imageUpdated['message'])
                && is_string($imageUpdated['message'])
                    ? $imageUpdated['message']
                    : 'プロフィール画像情報を保存できませんでした。';


            $auth->session()->flash(
                'error',
                $message
            );


            wp_safe_redirect(
                home_url('/profile/')
            );


            exit;
        }
    }
}


/*
|--------------------------------------------------------------------------
| 更新成功
|--------------------------------------------------------------------------
*/


$auth->session()->flash(
    'success',
    'プロフィールを更新しました。'
);


/*
|--------------------------------------------------------------------------
| プロフィール画面へ戻る
|--------------------------------------------------------------------------
*/


wp_safe_redirect(
    home_url('/profile/')
);


exit;