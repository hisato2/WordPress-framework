<?php

declare(strict_types=1);

use HKS\Products\SalesOptionService;

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
        'hks_create_subscription'
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
| Publication ID
|--------------------------------------------------------------------------
|
| 画面上では「対象刊行物」だが、
| DB上では hks_product_series.id を使用する。
|
*/

$seriesId = isset($_POST['publication_id'])
    ? absint($_POST['publication_id'])
    : 0;

if ($seriesId <= 0) {
    wp_die(
        '対象刊行物を選択してください。',
        '入力エラー',
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
| Create Subscription
|--------------------------------------------------------------------------
*/

try {

    $salesOptionService =
        new SalesOptionService();

    $salesOptionService
        ->createSubscriptionForSeries(
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
            'created' => '1',
        ],
        home_url('/dashboard/')
    );

    wp_safe_redirect($redirectUrl);

    exit;

} catch (\Throwable $e) {

    wp_die(
        esc_html($e->getMessage()),
        '定期購読登録エラー',
        ['response' => 400]
    );
}