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



    /*
    |--------------------------------------------------------------------------
    | 商品新規登録
    |--------------------------------------------------------------------------
    */

    if ($dashboardAction === 'hks_create_product') {
        $createProductFile = get_template_directory()
            . '/api/products/create.php';

        if (!is_file($createProductFile)) {
            wp_die(
                '商品登録処理が見つかりません。',
                'システムエラー',
                ['response' => 500]
            );
        }

        require $createProductFile;

        exit;
    }



    /*
    |--------------------------------------------------------------------------
    | シリーズ新規登録
    |--------------------------------------------------------------------------
    */

    if ($dashboardAction === 'hks_create_series') {

        $createSeriesFile = get_template_directory()
            . '/api/series/create.php';

        if (!is_file($createSeriesFile)) {
            wp_die(
                'シリーズ登録処理が見つかりません。',
                'システムエラー',
                ['response' => 500]
            );
        }

        require $createSeriesFile;

        exit;
    }




    /*
    |--------------------------------------------------------------------------
    | 商品更新
    |--------------------------------------------------------------------------
    */

    if ($dashboardAction === 'update_product') {
        $updateProductFile = get_template_directory()
            . '/api/products/update.php';

        if (!is_file($updateProductFile)) {
            wp_die(
                '商品更新処理が見つかりません。',
                'システムエラー',
                ['response' => 500]
            );
        }

        require $updateProductFile;

        exit;
    }





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
    | 商品管理
    |--------------------------------------------------------------------------
    */

    case 'products':

        $productRepository = new \HKS\Repositories\ProductRepository();

        $products = $productRepository->all();

        $contentView = get_template_directory()
            . '/views/dashboard/products/index.php';

        break;

   /*
    |--------------------------------------------------------------------------
    | 商品新規登録
    |--------------------------------------------------------------------------
    */

    case 'product-create':

        /*
        |--------------------------------------------------------------------------
        | シリーズ一覧取得
        |--------------------------------------------------------------------------
        |
        | 商品を既存シリーズへ所属させるため、
        | 商品登録画面へシリーズ一覧を渡す。
        |
        */

        $productSeriesRepository =
            new \HKS\Repositories\ProductSeriesRepository();

        $seriesList =
            $productSeriesRepository->all();


        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */

        $contentView = get_template_directory()
            . '/views/dashboard/products/create.php';

        break;
        

    /*
    |--------------------------------------------------------------------------
    | 商品編集
    |--------------------------------------------------------------------------
    */

    case 'product-edit':

        $productId = isset($_GET['id'])
            ? absint($_GET['id'])
            : 0;


        /*
        |--------------------------------------------------------------------------
        | 商品情報取得
        |--------------------------------------------------------------------------
        */

        $productRepository =
            new \HKS\Repositories\ProductRepository();

        $product =
            $productRepository->findById(
                $productId
            );


        /*
        |--------------------------------------------------------------------------
        | 単品販売条件取得
        |--------------------------------------------------------------------------
        |
        | hks_sales_options から、
        | この商品の single 販売条件を取得する。
        |
        | 販売条件がまだ登録されていない既存商品については
        | null のまま編集画面へ渡す。
        |
        */

        $salesOptionRepository =
            new \HKS\Repositories\SalesOptionRepository();

        $salesOption =
            $salesOptionRepository->findSingleByProductId(
                $productId
            );


        /*
        |--------------------------------------------------------------------------
        | View
        |--------------------------------------------------------------------------
        */

        $contentView = get_template_directory()
            . '/views/dashboard/products/edit.php';

        break;






        /*
        |--------------------------------------------------------------------------
        | シリーズ管理
        |--------------------------------------------------------------------------
        */

        case 'series':

            $productSeriesRepository =
                new \HKS\Repositories\ProductSeriesRepository();

            $seriesList =
                $productSeriesRepository->all();

            $contentView = get_template_directory()
                . '/views/dashboard/series/index.php';

            break;
                

        /*
        |--------------------------------------------------------------------------
        | シリーズ新規登録
        |--------------------------------------------------------------------------
        */

        case 'series-create':

            $contentView = get_template_directory()
                . '/views/dashboard/series/create.php';

            break;


/*
|--------------------------------------------------------------------------
| シリーズ編集
|--------------------------------------------------------------------------
*/

case 'series-edit':

    $seriesId = isset($_GET['id'])
        ? absint($_GET['id'])
        : 0;


    /*
    |--------------------------------------------------------------------------
    | Series ID Check
    |--------------------------------------------------------------------------
    */

    if ($seriesId <= 0) {
        wp_die(
            'シリーズIDが正しくありません。',
            'シリーズ取得エラー',
            ['response' => 400]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | シリーズ情報取得
    |--------------------------------------------------------------------------
    */

    $productSeriesRepository =
        new \HKS\Repositories\ProductSeriesRepository();

    $series =
        $productSeriesRepository->findById(
            $seriesId
        );


    if ($series === null) {
        wp_die(
            '指定されたシリーズが見つかりません。',
            'シリーズ取得エラー',
            ['response' => 404]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | 定期購読販売条件取得
    |--------------------------------------------------------------------------
    */

    $salesOptionRepository =
        new \HKS\Repositories\SalesOptionRepository();

    $subscription =
        $salesOptionRepository->findSubscriptionBySeriesId(
            $seriesId
        );


    /*
    |--------------------------------------------------------------------------
    | View
    |--------------------------------------------------------------------------
    */

    $contentView = get_template_directory()
        . '/views/dashboard/series/edit.php';

    break;
    




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