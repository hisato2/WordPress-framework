<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

/**
 * 会員プロフィール画像をアップロードする
 *
 * 保存先:
 * wp-content/uploads/hks-images/members/
 *
 * ファイル名:
 * 会員IDを4桁ゼロ埋めして使用する。
 *
 * 例:
 * ID 1    → 0001.webp
 * ID 25   → 0025.webp
 * ID 123  → 0123.webp
 * ID 1234 → 1234.webp
 *
 * ※画像形式はアップロードされた形式を維持する。
 * JPEG → .jpg
 * PNG  → .png
 * WebP → .webp
 *
 * 戻り値:
 * [
 *     'success' => true,
 *     'path'    => 'hks-images/members/0001.webp',
 *     'message' => 'プロフィール画像を保存しました。',
 * ]
 *
 * または
 *
 * [
 *     'success' => false,
 *     'message' => 'エラーメッセージ',
 * ]
 *
 * @param array<string, mixed> $file
 * @param int $memberId
 * @return array<string, mixed>
 */
function hks_upload_member_avatar(
    array $file,
    int $memberId
): array
{
    /*
    |--------------------------------------------------------------------------
    | 会員ID確認
    |--------------------------------------------------------------------------
    */

    if ($memberId <= 0) {
        return [
            'success' => false,
            'message' => '会員IDが正しくありません。',
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
            'message' => '画像ファイルが指定されていません。',
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
                => '画像ファイルのサイズが大きすぎます。',

            UPLOAD_ERR_PARTIAL
                => '画像ファイルを完全にアップロードできませんでした。',

            UPLOAD_ERR_NO_FILE
                => '画像ファイルが選択されていません。',

            default
                => '画像ファイルのアップロードに失敗しました。',
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
            'message' => 'アップロードされたファイルを確認できません。',
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
            'message' => '画像ファイルは5MB以下にしてください。',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | MIMEタイプ確認
    |--------------------------------------------------------------------------
    |
    | ブラウザから送信された type は信用せず、
    | WordPress側で実ファイルを確認する。
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
            'message' => 'JPEG、PNG、WebP形式の画像を選択してください。',
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
            'message' => 'アップロード先を取得できませんでした。',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | 保存先
    |--------------------------------------------------------------------------
    */

    $relativeDirectory = 'hks-images/members';

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
            'message' => '画像保存フォルダを作成できませんでした。',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | ファイル名生成
    |--------------------------------------------------------------------------
    |
    | 会員IDを4桁ゼロ埋めして使用する。
    |
    | 例:
    | ID 1    → 0001.jpg / 0001.png / 0001.webp
    | ID 25   → 0025.jpg / 0025.png / 0025.webp
    | ID 123  → 0123.jpg / 0123.png / 0123.webp
    | ID 1234 → 1234.jpg / 1234.png / 1234.webp
    |
    */

    $extension = $allowedTypes[$mimeType];

    $fileName = sprintf(
        '%04d.%s',
        $memberId,
        $extension
    );

    /*
    |--------------------------------------------------------------------------
    | 保存先ファイルパス
    |--------------------------------------------------------------------------
    */

    $targetPath = trailingslashit(
        $targetDirectory
    ) . $fileName;

    /*
    |--------------------------------------------------------------------------
    | 同一会員の別拡張子画像を削除
    |--------------------------------------------------------------------------
    |
    | 例:
    | 既存: 0001.jpg
    | 新規: 0001.webp
    |
    | の場合、古い 0001.jpg を削除する。
    |
    */

    $baseFileName = sprintf(
        '%04d',
        $memberId
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
    | ファイル移動
    |--------------------------------------------------------------------------
    |
    | 同じ会員ID・同じ拡張子の画像が存在する場合は上書きする。
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
            'message' => '画像ファイルを保存できませんでした。',
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | 保存したファイルのパーミッション調整
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
    | Windowsの物理パスやURLはDBへ保存しない。
    |
    | 例:
    | hks-images/members/0001.webp
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
        'message' => 'プロフィール画像を保存しました。',
    ];
}

