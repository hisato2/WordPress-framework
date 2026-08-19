<?php

declare(strict_types=1);

use HKS\Products\ProductService;
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

$nonce = isset($_POST['hks_create_product_nonce'])
    ? sanitize_text_field(
        wp_unslash($_POST['hks_create_product_nonce'])
    )
    : '';

if (
    $nonce === ''
    || !wp_verify_nonce(
        $nonce,
        'hks_create_product_action'
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
| Input
|--------------------------------------------------------------------------
*/

$data = [

    'product_code' => isset($_POST['product_code'])
        ? sanitize_text_field(
            wp_unslash($_POST['product_code'])
        )
        : '',

    'product_type' => isset($_POST['product_type'])
        ? sanitize_key(
            wp_unslash($_POST['product_type'])
        )
        : '',

    /*
    |--------------------------------------------------------------------------
    | Product Name
    |--------------------------------------------------------------------------
    |
    | WordPress予約クエリ変数 "name" との衝突を避けるため、
    | POSTでは product_name を使用する。
    |
    | ProductServiceへ渡す際はDB項目名の name に戻す。
    |
    */

    'name' => isset($_POST['product_name'])
        ? sanitize_text_field(
            wp_unslash($_POST['product_name'])
        )
        : '',

    'description' => isset($_POST['description'])
        ? sanitize_textarea_field(
            wp_unslash($_POST['description'])
        )
        : '',

    'series_id' => (
        isset($_POST['series_id'])
        && $_POST['series_id'] !== ''
    )
        ? absint($_POST['series_id'])
        : null,
        

    'isbn' => isset($_POST['isbn'])
        ? sanitize_text_field(
            wp_unslash($_POST['isbn'])
        )
        : '',

    'volume_number' => isset($_POST['volume_number'])
        ? sanitize_text_field(
            wp_unslash($_POST['volume_number'])
        )
        : '',

    'issue_number' => isset($_POST['issue_number'])
        ? sanitize_text_field(
            wp_unslash($_POST['issue_number'])
        )
        : '',

    'publication_year' => isset($_POST['publication_year'])
        ? sanitize_text_field(
            wp_unslash($_POST['publication_year'])
        )
        : '',

    'publication_month' => isset($_POST['publication_month'])
        ? sanitize_text_field(
            wp_unslash($_POST['publication_month'])
        )
        : '',

    'publication_date' => isset($_POST['publication_date'])
        ? sanitize_text_field(
            wp_unslash($_POST['publication_date'])
        )
        : '',

    'tax_rate' => isset($_POST['tax_rate'])
        ? sanitize_text_field(
            wp_unslash($_POST['tax_rate'])
        )
        : '',

    'tax_type' => isset($_POST['tax_type'])
        ? sanitize_key(
            wp_unslash($_POST['tax_type'])
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
| Sales Option Input
|--------------------------------------------------------------------------
|
| 商品本体とは別テーブル hks_sales_options に保存する
| 単品販売用の販売設定。
|
*/

$salesData = [

    'price' => isset($_POST['sales_price'])
        ? sanitize_text_field(
            wp_unslash($_POST['sales_price'])
        )
        : '',

    'name' => '単品購入',

    'shipping_policy' => isset($_POST['shipping_policy'])
        ? sanitize_key(
            wp_unslash($_POST['shipping_policy'])
        )
        : 'per_order',

    'shipping_amount' => isset($_POST['shipping_amount'])
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
| Create
|--------------------------------------------------------------------------
*/

try {

    $productService = new ProductService();


    /*
    |--------------------------------------------------------------------------
    | 商品基本情報登録
    |--------------------------------------------------------------------------
    |
    | 商品画像・お試しPDFのファイル名に商品IDを使用するため、
    | 先に商品をDBへ登録して商品IDを取得する。
    |
    */

    $productId = $productService->create(
        $data
    );


        /*
    |--------------------------------------------------------------------------
    | 単品販売条件登録
    |--------------------------------------------------------------------------
    |
    | 商品本体の登録によって取得した商品IDを使用して、
    | hks_sales_options に単品販売条件を登録する。
    |
    */

    $salesOptionService = new SalesOptionService();

    $salesOptionService->createSingleForProduct(
        $productId,
        $salesData
    );


    /*
    |--------------------------------------------------------------------------
    | 商品画像
    |--------------------------------------------------------------------------
    |
    | 商品画像が選択されている場合のみ、
    | アップロード処理を実行する。
    |
    */

    if (
        isset($_FILES['product_image'])
        && is_array($_FILES['product_image'])
        && isset($_FILES['product_image']['error'])
        && (int) $_FILES['product_image']['error'] !== UPLOAD_ERR_NO_FILE
    ) {

        /*
        |--------------------------------------------------------------------------
        | 商品画像アップロード処理読み込み
        |--------------------------------------------------------------------------
        */

        $productImageFile = dirname(__DIR__)
            . '/upload/product-image.php';


        if (!is_file($productImageFile)) {
            throw new \RuntimeException(
                '商品画像アップロード処理が見つかりません。'
            );
        }


        require_once $productImageFile;


        /*
        |--------------------------------------------------------------------------
        | 商品画像アップロード
        |--------------------------------------------------------------------------
        */

        $uploadResult = hks_upload_product_image(
            $_FILES['product_image'],
            $productId
        );


        if (empty($uploadResult['success'])) {

            $message = isset($uploadResult['message'])
                && is_string($uploadResult['message'])
                    ? $uploadResult['message']
                    : '商品画像を保存できませんでした。';


            throw new \RuntimeException(
                $message
            );
        }


        /*
        |--------------------------------------------------------------------------
        | 商品画像パス取得
        |--------------------------------------------------------------------------
        */

        $imagePath = isset($uploadResult['path'])
            && is_string($uploadResult['path'])
                ? $uploadResult['path']
                : '';


        if ($imagePath === '') {
            throw new \RuntimeException(
                '商品画像の保存パスを取得できませんでした。'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | 商品画像パス保存
        |--------------------------------------------------------------------------
        */

        $productService->update(
            $productId,
            [
                'image_path' => $imagePath,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | お試しPDF
    |--------------------------------------------------------------------------
    |
    | お試しPDFが選択されている場合のみ、
    | アップロード処理を実行する。
    |
    */

    if (
        isset($_FILES['preview_pdf'])
        && is_array($_FILES['preview_pdf'])
        && isset($_FILES['preview_pdf']['error'])
        && (int) $_FILES['preview_pdf']['error'] !== UPLOAD_ERR_NO_FILE
    ) {

        /*
        |--------------------------------------------------------------------------
        | お試しPDFアップロード処理読み込み
        |--------------------------------------------------------------------------
        */

        $previewPdfFile = dirname(__DIR__)
            . '/upload/product-preview-pdf.php';


        if (!is_file($previewPdfFile)) {
            throw new \RuntimeException(
                'お試しPDFアップロード処理が見つかりません。'
            );
        }


        require_once $previewPdfFile;


        /*
        |--------------------------------------------------------------------------
        | お試しPDFアップロード
        |--------------------------------------------------------------------------
        */

        $pdfUploadResult = hks_upload_product_preview_pdf(
            $_FILES['preview_pdf'],
            $productId
        );


        if (empty($pdfUploadResult['success'])) {

            $message = isset($pdfUploadResult['message'])
                && is_string($pdfUploadResult['message'])
                    ? $pdfUploadResult['message']
                    : 'お試しPDFを保存できませんでした。';


            throw new \RuntimeException(
                $message
            );
        }


        /*
        |--------------------------------------------------------------------------
        | お試しPDFパス取得
        |--------------------------------------------------------------------------
        */

        $previewPdfPath = isset($pdfUploadResult['path'])
            && is_string($pdfUploadResult['path'])
                ? $pdfUploadResult['path']
                : '';


        if ($previewPdfPath === '') {
            throw new \RuntimeException(
                'お試しPDFの保存パスを取得できませんでした。'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | お試しPDFパス保存
        |--------------------------------------------------------------------------
        */

        $productService->update(
            $productId,
            [
                'preview_pdf_path' => $previewPdfPath,
            ]
        );
    }


    /*
    |--------------------------------------------------------------------------
    | お試しPDF公開設定
    |--------------------------------------------------------------------------
    |
    | checkboxは未チェックの場合POSTされないため、
    | 存在して値が1なら公開、それ以外は非公開として保存する。
    |
    */

    $previewEnabled = isset($_POST['preview_enabled'])
        && (string) wp_unslash($_POST['preview_enabled']) === '1'
            ? 1
            : 0;


    $productService->update(
        $productId,
        [
            'preview_enabled' => $previewEnabled,
        ]
    );


} catch (\InvalidArgumentException $e) {

    wp_die(
        esc_html($e->getMessage()),
        '入力エラー',
        ['response' => 400]
    );


} catch (\Throwable $e) {

    /*
    |--------------------------------------------------------------------------
    | 開発用エラー表示
    |--------------------------------------------------------------------------
    |
    | 現在は開発中のため詳細を表示する。
    | 本番公開時には詳細情報を表示しない実装へ戻すこと。
    |
    */

    wp_die(
        '<pre>'
        . esc_html(
            get_class($e)
            . "\n\n"
            . $e->getMessage()
            . "\n\nFile: "
            . $e->getFile()
            . "\nLine: "
            . $e->getLine()
            . "\n\n"
            . $e->getTraceAsString()
        )
        . '</pre>',
        'PRODUCT CREATE DEBUG',
        ['response' => 500]
    );
}


/*
|--------------------------------------------------------------------------
| Redirect
|--------------------------------------------------------------------------
*/

$redirectUrl = add_query_arg(
    [
        'view'    => 'product-edit',
        'id'      => $productId,
        'created' => '1',
    ],
    home_url('/dashboard/')
);


wp_safe_redirect(
    $redirectUrl
);

exit;