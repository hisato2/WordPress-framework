<?php

declare(strict_types=1);

defined('ABSPATH') || exit;


/**
 * 商品のお試しPDFをアップロードする。
 *
 * 保存先:
 * wp-content/uploads/hks-pdfs/products/
 *
 * ファイル名:
 * 商品IDを4桁ゼロ埋めして使用する。
 *
 * 例:
 * ID 1    → 0001.pdf
 * ID 25   → 0025.pdf
 * ID 123  → 0123.pdf
 *
 * 対応形式:
 * PDF
 *
 * 最大サイズ:
 * 20MB
 *
 * @param array<string, mixed> $file
 * @param int $productId
 * @return array<string, mixed>
 */
function hks_upload_product_preview_pdf(
    array $file,
    int $productId
): array
{
    /*
    |--------------------------------------------------------------------------
    | 商品ID確認
    |--------------------------------------------------------------------------
    */

    if ($productId <= 0) {
        return [
            'success' => false,
            'message' => '商品IDが正しくありません。',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | ファイル存在確認
    |--------------------------------------------------------------------------
    */

    if (
        empty($file)
        || !isset($file['error'])
    ) {
        return [
            'success' => false,
            'message' => 'お試しPDFが指定されていません。',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | PHPアップロードエラー確認
    |--------------------------------------------------------------------------
    */

    $error = (int) $file['error'];

    if ($error !== UPLOAD_ERR_OK) {

        $message = match ($error) {

            UPLOAD_ERR_INI_SIZE,
            UPLOAD_ERR_FORM_SIZE
                => 'お試しPDFのサイズが大きすぎます。',

            UPLOAD_ERR_PARTIAL
                => 'お試しPDFを完全にアップロードできませんでした。',

            UPLOAD_ERR_NO_FILE
                => 'お試しPDFが選択されていません。',

            default
                => 'お試しPDFのアップロードに失敗しました。',
        };

        return [
            'success' => false,
            'message' => $message,
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | 一時ファイル確認
    |--------------------------------------------------------------------------
    */

    $tmpName = isset($file['tmp_name'])
        ? (string) $file['tmp_name']
        : '';

    if (
        $tmpName === ''
        || !is_uploaded_file($tmpName)
    ) {
        return [
            'success' => false,
            'message' => 'アップロードされたお試しPDFを確認できません。',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | ファイルサイズ確認
    |--------------------------------------------------------------------------
    |
    | 最大20MB
    |
    */

    $fileSize = isset($file['size'])
        ? (int) $file['size']
        : 0;

    $maxFileSize = 20 * 1024 * 1024;

    if (
        $fileSize <= 0
        || $fileSize > $maxFileSize
    ) {
        return [
            'success' => false,
            'message' => 'お試しPDFは20MB以下にしてください。',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | MIMEタイプ・拡張子確認
    |--------------------------------------------------------------------------
    |
    | $_FILES['type'] は信用せず、
    | 実際にアップロードされたファイルを
    | WordPress側で確認する。
    |
    */

    $fileType = wp_check_filetype_and_ext(
        $tmpName,
        isset($file['name'])
            ? (string) $file['name']
            : ''
    );

    $mimeType = isset($fileType['type'])
        ? (string) $fileType['type']
        : '';

    $extension = isset($fileType['ext'])
        ? strtolower((string) $fileType['ext'])
        : '';

    if (
        $mimeType !== 'application/pdf'
        || $extension !== 'pdf'
    ) {
        return [
            'success' => false,
            'message' => 'PDF形式のファイルを選択してください。',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | WordPress Uploadディレクトリ取得
    |--------------------------------------------------------------------------
    */

    $uploadDir = wp_upload_dir();

    if (!empty($uploadDir['error'])) {
        return [
            'success' => false,
            'message' => 'お試しPDFのアップロード先を取得できませんでした。',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | 保存先
    |--------------------------------------------------------------------------
    */

    $relativeDirectory = 'hks-pdfs/products';

    $targetDirectory = trailingslashit(
        (string) $uploadDir['basedir']
    ) . $relativeDirectory;


    /*
    |--------------------------------------------------------------------------
    | 保存先ディレクトリ作成
    |--------------------------------------------------------------------------
    */

    if (
        !is_dir($targetDirectory)
        && !wp_mkdir_p($targetDirectory)
    ) {
        return [
            'success' => false,
            'message' => 'お試しPDFの保存フォルダを作成できませんでした。',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | ファイル名生成
    |--------------------------------------------------------------------------
    */

    $fileName = sprintf(
        '%04d.pdf',
        $productId
    );

    $targetPath = trailingslashit(
        $targetDirectory
    ) . $fileName;


    /*
    |--------------------------------------------------------------------------
    | ファイル保存
    |--------------------------------------------------------------------------
    |
    | 同じ商品IDのPDFが存在する場合は、
    | 新しいPDFで上書きする。
    |
    */

    if (
        !move_uploaded_file(
            $tmpName,
            $targetPath
        )
    ) {
        return [
            'success' => false,
            'message' => 'お試しPDFを保存できませんでした。',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | パーミッション調整
    |--------------------------------------------------------------------------
    */

    $stat = stat(
        dirname($targetPath)
    );

    if ($stat !== false) {

        $permissions = $stat['mode'] & 0000666;

        @chmod(
            $targetPath,
            $permissions
        );
    }


    /*
    |--------------------------------------------------------------------------
    | DB保存用相対パス
    |--------------------------------------------------------------------------
    |
    | DBにはURLやWindowsの物理パスではなく、
    | uploadsディレクトリからの相対パスを保存する。
    |
    | 例:
    | hks-pdfs/products/0025.pdf
    |
    */

    $relativePath = $relativeDirectory
        . '/'
        . $fileName;


    /*
    |--------------------------------------------------------------------------
    | 成功
    |--------------------------------------------------------------------------
    */

    return [
        'success' => true,
        'path'    => $relativePath,
        'message' => 'お試しPDFを保存しました。',
    ];
}