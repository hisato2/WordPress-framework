<?php

declare(strict_types=1);

use HKS\Products\ProductSeriesService;

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
        'hks_update_series'
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
| Series ID
|--------------------------------------------------------------------------
*/

$seriesId = isset($_POST['series_id'])
    ? absint($_POST['series_id'])
    : 0;

if ($seriesId <= 0) {
    wp_die(
        'シリーズIDが正しくありません。',
        '入力エラー',
        ['response' => 400]
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
| Update Series
|--------------------------------------------------------------------------
*/

try {

    $productSeriesService =
        new ProductSeriesService();

    $productSeriesService->update(
        $seriesId,
        $seriesData
    );

    /*
    |--------------------------------------------------------------------------
    | Redirect
    |--------------------------------------------------------------------------
    */

    $redirectUrl = add_query_arg(
        [
            'view'    => 'series',
            'updated' => '1',
        ],
        home_url('/dashboard/')
    );

    wp_safe_redirect($redirectUrl);

    exit;
} catch (\Throwable $e) {

    wp_die(
        esc_html($e->getMessage()),
        'シリーズ更新エラー',
        ['response' => 400]
    );
}
