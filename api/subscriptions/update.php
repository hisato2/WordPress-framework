<?php

declare(strict_types=1);

use HKS\Products\SalesOptionService;
use HKS\Repositories\SalesOptionRepository;

defined('ABSPATH') || exit;


/*
|--------------------------------------------------------------------------
| Request Method
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    wp_die(
        '不正なリクエストです。',
        'リクエストエラー',
        ['response' => 405]
    );
}


/*
|--------------------------------------------------------------------------
| Nonce Check
|--------------------------------------------------------------------------
*/

$nonce = isset($_POST['hks_subscription_nonce'])
    ? sanitize_text_field(
        wp_unslash($_POST['hks_subscription_nonce'])
    )
    : '';

if (
    $nonce === ''
    || !wp_verify_nonce(
        $nonce,
        'hks_update_subscription'
    )
) {
    wp_die(
        'セキュリティ確認に失敗しました。',
        '認証エラー',
        ['response' => 403]
    );
}


/*
|--------------------------------------------------------------------------
| Subscription ID
|--------------------------------------------------------------------------
*/

$subscriptionId = isset($_POST['subscription_id'])
    ? absint($_POST['subscription_id'])
    : 0;

if ($subscriptionId <= 0) {
    wp_die(
        '定期購読IDが正しくありません。',
        '入力エラー',
        ['response' => 400]
    );
}


/*
|--------------------------------------------------------------------------
| Existing Subscription
|--------------------------------------------------------------------------
*/

$salesOptionRepository =
    new SalesOptionRepository();

$existingSubscription =
    $salesOptionRepository->findById(
        $subscriptionId
    );

if ($existingSubscription === null) {
    wp_die(
        '定期購読情報が見つかりません。',
        'データエラー',
        ['response' => 404]
    );
}

if (
    ($existingSubscription['sales_type'] ?? '')
    !== 'subscription'
) {
    wp_die(
        '指定された販売条件は定期購読ではありません。',
        'データエラー',
        ['response' => 400]
    );
}


/*
|--------------------------------------------------------------------------
| Series ID
|--------------------------------------------------------------------------
|
| 編集画面からseries_idを受け取らず、
| DBに保存されているseries_idを使用する。
| これにより対象刊行物を変更させない。
|
*/

$seriesId = (int) (
    $existingSubscription['series_id'] ?? 0
);

if ($seriesId <= 0) {
    wp_die(
        '対象刊行物が正しくありません。',
        'データエラー',
        ['response' => 400]
    );
}


/*
|--------------------------------------------------------------------------
| Subscription Input
|--------------------------------------------------------------------------
*/

$subscriptionData = [

    'name' => isset($_POST['subscription_name'])
        ? sanitize_text_field(
            wp_unslash($_POST['subscription_name'])
        )
        : '',

    'price' => isset($_POST['subscription_price'])
        ? sanitize_text_field(
            wp_unslash($_POST['subscription_price'])
        )
        : '',

    'subscription_months' => isset(
        $_POST['subscription_months']
    )
        ? sanitize_text_field(
            wp_unslash($_POST['subscription_months'])
        )
        : '',

    'scheduled_shipments' => isset(
        $_POST['scheduled_shipments']
    )
        ? sanitize_text_field(
            wp_unslash($_POST['scheduled_shipments'])
        )
        : '',

    'shipping_policy' => isset($_POST['shipping_policy'])
        ? sanitize_key(
            wp_unslash($_POST['shipping_policy'])
        )
        : '',

    'shipping_amount' => isset($_POST['shipping_amount'])
        ? sanitize_text_field(
            wp_unslash($_POST['shipping_amount'])
        )
        : '0',

    'status' => isset($_POST['sales_status'])
        ? sanitize_key(
            wp_unslash($_POST['sales_status'])
        )
        : '',
];


/*
|--------------------------------------------------------------------------
| Update Subscription
|--------------------------------------------------------------------------
*/

try {

    $salesOptionService =
        new SalesOptionService();

    $salesOptionService
        ->updateSubscriptionForSeries(
            $seriesId,
            $subscriptionData
        );


    /*
    |--------------------------------------------------------------------------
    | Redirect
    |--------------------------------------------------------------------------
    */

    $redirectUrl = add_query_arg(
        [
            'view'    => 'subscriptions',
            'updated' => '1',
        ],
        home_url('/dashboard/')
    );

    wp_safe_redirect($redirectUrl);

    exit;

} catch (\Throwable $e) {

    wp_die(
        esc_html($e->getMessage()),
        '定期購読更新エラー',
        ['response' => 400]
    );
}