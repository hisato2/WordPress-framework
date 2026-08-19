<?php

declare(strict_types=1);

use HKS\Products\ProductSeriesService;
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

$nonce = isset($_POST['hks_series_nonce'])
    ? sanitize_text_field(
        wp_unslash($_POST['hks_series_nonce'])
    )
    : '';

if (
    $nonce === ''
    || !wp_verify_nonce(
        $nonce,
        'hks_create_series'
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
| Series Input
|--------------------------------------------------------------------------
*/

$seriesData = [

    'series_code' => isset($_POST['series_code'])
        ? sanitize_text_field(
            wp_unslash($_POST['series_code'])
        )
        : '',

    'series_type' => isset($_POST['series_type'])
        ? sanitize_key(
            wp_unslash($_POST['series_type'])
        )
        : '',

    'name' => isset($_POST['series_name'])
        ? sanitize_text_field(
            wp_unslash($_POST['series_name'])
        )
        : '',

    'description' => isset($_POST['description'])
        ? sanitize_textarea_field(
            wp_unslash($_POST['description'])
        )
        : '',

    'issn' => isset($_POST['issn'])
        ? sanitize_text_field(
            wp_unslash($_POST['issn'])
        )
        : '',

    'status' => isset($_POST['series_status'])
        ? sanitize_key(
            wp_unslash($_POST['series_status'])
        )
        : 'draft',
];


/*
|--------------------------------------------------------------------------
| Subscription Enabled
|--------------------------------------------------------------------------
*/

$subscriptionEnabled =
    isset($_POST['subscription_enabled'])
    && (string) $_POST['subscription_enabled'] === '1';


/*
|--------------------------------------------------------------------------
| Subscription Input
|--------------------------------------------------------------------------
|
| チェックされた場合のみ使用する。
| 実際の開始号・終了号はここでは登録しない。
| 購入時に購買者ごとの購読契約として管理する。
|
*/

$subscriptionData = [

    'name' => isset($_POST['subscription_name'])
        ? sanitize_text_field(
            wp_unslash($_POST['subscription_name'])
        )
        : '年間購読',

    'price' => isset($_POST['subscription_price'])
        ? sanitize_text_field(
            wp_unslash($_POST['subscription_price'])
        )
        : '',

    'subscription_months' =>
        isset($_POST['subscription_months'])
            ? sanitize_text_field(
                wp_unslash($_POST['subscription_months'])
            )
            : '',

    'scheduled_shipments' =>
        isset($_POST['scheduled_shipments'])
            ? sanitize_text_field(
                wp_unslash($_POST['scheduled_shipments'])
            )
            : '',

    'shipping_policy' =>
        isset($_POST['shipping_policy'])
            ? sanitize_key(
                wp_unslash($_POST['shipping_policy'])
            )
            : 'included',

    'shipping_amount' =>
        isset($_POST['shipping_amount'])
            ? sanitize_text_field(
                wp_unslash($_POST['shipping_amount'])
            )
            : '0',

    'status' => isset($_POST['sales_status'])
        ? sanitize_key(
            wp_unslash($_POST['sales_status'])
        )
        : 'active',
];


/*
|--------------------------------------------------------------------------
| Create Series
|--------------------------------------------------------------------------
*/

try {

    $productSeriesService =
        new ProductSeriesService();

    $seriesId =
        $productSeriesService->create(
            $seriesData
        );


    /*
    |--------------------------------------------------------------------------
    | Create Subscription
    |--------------------------------------------------------------------------
    |
    | 書籍シリーズなど、定期購読を使用しないシリーズでは
    | subscription_enabled がOFFなので作成しない。
    |
    */

    if ($subscriptionEnabled) {

        $salesOptionService =
            new SalesOptionService();

        $salesOptionService->createSubscriptionForSeries(
            $seriesId,
            $subscriptionData
        );
    }


    /*
    |--------------------------------------------------------------------------
    | Redirect
    |--------------------------------------------------------------------------
    */

    $redirectUrl = add_query_arg(
        [
            'view'    => 'series',
            'created' => '1',
        ],
        home_url('/dashboard/')
    );

    wp_safe_redirect($redirectUrl);

    exit;

} catch (\Throwable $e) {

    wp_die(
        esc_html($e->getMessage()),
        'シリーズ登録エラー',
        ['response' => 400]
    );
}