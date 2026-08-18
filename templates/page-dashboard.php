<?php

declare(strict_types=1);

/**
 * Template Name: Dashboard
 * Template Post Type: page
 */

$auth = new \HKS\Auth\Auth();

/*
|--------------------------------------------------------------------------
| ログイン確認
|--------------------------------------------------------------------------
*/

$auth->requireLogin();

/*
|--------------------------------------------------------------------------
| ダッシュボード権限確認
|--------------------------------------------------------------------------
*/

if (!$auth->canAccessAdmin()) {
    wp_safe_redirect(home_url('/mypage/'));
    exit;
}


/*
|--------------------------------------------------------------------------
| POST処理
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| POST処理
|--------------------------------------------------------------------------
|
| Dashboardページ自身でPOSTを受け取り、
| actionの内容に応じて各API処理へ振り分ける。
|
| WordPressの admin-post.php は使用しない。
|
*/


if ($_SERVER['REQUEST_METHOD'] === 'POST') {


    /*
    |--------------------------------------------------------------------------
    | 会員プロフィール更新
    |--------------------------------------------------------------------------
    |
    | 既存の会員詳細画面は
    | name="action"
    | value="hks_update_member_profile"
    | で送信しているため、現時点では既存仕様を維持する。
    |
    */


    $action = isset($_POST['action'])
        ? sanitize_key(
            wp_unslash($_POST['action'])
        )
        : '';


    if ($action === 'hks_update_member_profile') {
        $updateFile = get_template_directory()
            . '/api/members/update-profile.php';


        if (!is_file($updateFile)) {
            wp_die(
                '会員情報更新処理が見つかりません。',
                'システムエラー',
                ['response' => 500]
            );
        }


        require $updateFile;


        exit;
    }



    /*
    |--------------------------------------------------------------------------
    | 会員パスワード変更
    |--------------------------------------------------------------------------
    */


    $dashboardAction = isset($_POST['hks_dashboard_action'])
        ? sanitize_key(
            wp_unslash($_POST['hks_dashboard_action'])
        )
        : '';


    if ($dashboardAction === 'change_member_password') {
        $changePasswordFile = get_template_directory()
            . '/api/members/change-password.php';


        if (!is_file($changePasswordFile)) {
            wp_die(
                '会員パスワード変更処理が見つかりません。',
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
| ダッシュボード権限確認
|--------------------------------------------------------------------------
*/

if (!$auth->canAccessAdmin()) {

    wp_safe_redirect(home_url('/mypage/'));
    exit;
}

/*
|--------------------------------------------------------------------------
| ログインユーザー取得
|--------------------------------------------------------------------------
*/

$user = $auth->user();

/*
|--------------------------------------------------------------------------
| 表示画面取得
|--------------------------------------------------------------------------
|
| /dashboard/
|     → home
|
| /dashboard/?view=users
|     → users
|
*/

$view = isset($_GET['view'])
    ? sanitize_key(wp_unslash($_GET['view']))
    : 'home';

/*
|--------------------------------------------------------------------------
| Content View
|--------------------------------------------------------------------------
|
| URLからPHPファイル名を直接指定させず、
| 許可した画面だけを明示的に振り分ける。
|
*/

switch ($view) {

    /*
    |--------------------------------------------------------------------------
    | 会員管理
    |--------------------------------------------------------------------------
    */

    case 'users':

        $userRepository = new \HKS\Repositories\UserRepository();

        $users = $userRepository->all();

        $contentView = get_template_directory()
            . '/views/dashboard/users/index.php';

        break;


    /*
    |--------------------------------------------------------------------------
    | 会員詳細
    |--------------------------------------------------------------------------
    */

    case 'user-detail':

        $userId = isset($_GET['id'])
            ? absint($_GET['id'])
            : 0;

        $userRepository = new \HKS\Repositories\UserRepository();

        $member = $userRepository->findById($userId);

        $contentView = get_template_directory()
            . '/views/dashboard/users/detail.php';

        break;

    /*
    |--------------------------------------------------------------------------
    | 会員パスワード変更
    |--------------------------------------------------------------------------
    */


    case 'user-password':


        $userId = isset($_GET['id'])
            ? absint($_GET['id'])
            : 0;


        $userRepository = new \HKS\Repositories\UserRepository();


        $member = $userRepository->findById($userId);


        $contentView = get_template_directory()
            . '/views/dashboard/users/password.php';


        break;

    /*
    |--------------------------------------------------------------------------
    | ダッシュボードTOP
    |--------------------------------------------------------------------------
    */

    case 'home':

    default:

        $contentView = get_template_directory()
            . '/views/dashboard/home.php';

        break;
}

/*
|--------------------------------------------------------------------------
| View表示
|--------------------------------------------------------------------------
*/

get_header();

require get_template_directory()
    . '/views/layouts/dashboard.php';

get_footer();