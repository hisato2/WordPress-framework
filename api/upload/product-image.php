<?php

declare(strict_types=1);

defined('ABSPATH') || exit;


/**
 * 商品画像をアップロードする。
 *
 * 保存先:
 * wp-content/uploads/hks-images/products/
 *
 * ファイル名:
 * 商品IDを4桁ゼロ埋めして使用する。
 *
 * 例:
 * ID 1    → 0001.jpg
 * ID 25   → 0025.png
 * ID 123  → 0123.webp
 *
 * 対応形式:
 * JPEG / PNG / WebP
 *
 * 最大サイズ:
 * 5MB
 *
 * @param array<string, mixed> $file
 * @param int $productId
 * @return array<string, mixed>
 */
function hks_upload_product_image(
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
            'message' => '商品画像が指定されていません。',
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
                => '商品画像のサイズが大きすぎます。',

            UPLOAD_ERR_PARTIAL
                => '商品画像を完全にアップロードできませんでした。',

            UPLOAD_ERR_NO_FILE
                => '商品画像が選択されていません。',

            default
                => '商品画像のアップロードに失敗しました。',
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
            'message' => 'アップロードされた商品画像を確認できません。',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | ファイルサイズ確認
    |--------------------------------------------------------------------------
    |
    | 最大5MB
    |
    */

    $fileSize = isset($file['size'])
        ? (int) $file['size']
        : 0;

    $maxFileSize = 5 * 1024 * 1024;

    if (
        $fileSize <= 0
        || $fileSize > $maxFileSize
    ) {
        return [
            'success' => false,
            'message' => '商品画像は5MB以下にしてください。',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | MIMEタイプ確認
    |--------------------------------------------------------------------------
    |
    | $_FILES['type'] は信用せず、
    | 実際のファイルをWordPress側で確認する。
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
        ? (string) $fileType['ext']
        : '';

    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
    ];

    if (
        !isset($allowedTypes[$mimeType])
        || $extension === ''
    ) {
        return [
            'success' => false,
            'message' => 'JPEG、PNG、WebP形式の商品画像を選択してください。',
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
            'message' => '商品画像のアップロード先を取得できませんでした。',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | 保存先
    |--------------------------------------------------------------------------
    */

    $relativeDirectory = 'hks-images/products';

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
            'message' => '商品画像の保存フォルダを作成できませんでした。',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | ファイル名生成
    |--------------------------------------------------------------------------
    */

    $extension = $allowedTypes[$mimeType];

    $fileName = sprintf(
        '%04d.%s',
        $productId,
        $extension
    );

    $targetPath = trailingslashit(
        $targetDirectory
    ) . $fileName;


    /*
    |--------------------------------------------------------------------------
    | 別拡張子の旧画像を削除
    |--------------------------------------------------------------------------
    |
    | 例:
    | 0001.jpg → 0001.webp
    |
    | へ変更した場合、古いjpgを削除する。
    |
    */

    $baseFileName = sprintf(
        '%04d',
        $productId
    );

    foreach (array_values($allowedTypes) as $oldExtension) {

        $oldPath = trailingslashit(
            $targetDirectory
        ) . $baseFileName . '.' . $oldExtension;

        if (
            $oldPath !== $targetPath
            && is_file($oldPath)
        ) {
            @unlink($oldPath);
        }
    }


    /*
    |--------------------------------------------------------------------------
    | ファイル保存
    |--------------------------------------------------------------------------
    */

    if (
        !move_uploaded_file(
            $tmpName,
            $targetPath
        )
    ) {
        return [
            'success' => false,
            'message' => '商品画像を保存できませんでした。',
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
    | URLやWindowsの物理パスは返さない。
    |
    | 例:
    | hks-images/products/0001.webp
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
        'message' => '商品画像を保存しました。',
    ];
}