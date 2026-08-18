<?php

declare(strict_types=1);


/**
 * Template Name: Profile
 * Template Post Type: page
 */

defined('ABSPATH') || exit;


/*
|--------------------------------------------------------------------------
| Authentication
|--------------------------------------------------------------------------
*/

$auth = new \HKS\Auth\Auth();


$auth->requireLogin();


/*
|--------------------------------------------------------------------------
| POST処理
|--------------------------------------------------------------------------
|
| Profileページ自身でPOSTを受け取り、
| actionの内容に応じて各API処理へ振り分ける。
|
| WordPressの admin-post.php は使用しない。
|
*/

if (
    $_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_POST['hks_profile_action'])
) {
    $profileAction = sanitize_key(
        wp_unslash($_POST['hks_profile_action'])
    );


    /*
    |--------------------------------------------------------------------------
    | プロフィール更新
    |--------------------------------------------------------------------------
    */

    if ($profileAction === 'update') {
        $updateFile = get_template_directory()
            . '/api/profile/update.php';


        if (!is_file($updateFile)) {
            wp_die(
                'プロフィール更新処理が見つかりません。',
                'システムエラー',
                ['response' => 500]
            );
        }


        require $updateFile;


        exit;
    }


    /*
    |--------------------------------------------------------------------------
    | パスワード変更
    |--------------------------------------------------------------------------
    */

    if ($profileAction === 'change_password') {
        $changePasswordFile = get_template_directory()
            . '/api/profile/change-password.php';


        if (!is_file($changePasswordFile)) {
            wp_die(
                'パスワード変更処理が見つかりません。',
                'システムエラー',
                ['response' => 500]
            );
        }


        require $changePasswordFile;


        exit;
    }
}


/*
|--------------------------------------------------------------------------
| ログインユーザー取得
|--------------------------------------------------------------------------
*/

$user = $auth->user();


/*
|--------------------------------------------------------------------------
| プロフィール画面表示
|--------------------------------------------------------------------------
*/

get_header();


require get_template_directory()
    . '/views/profile/index.php';


get_footer();