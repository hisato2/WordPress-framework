<?php

declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require_once dirname(__DIR__, 5) . '/wp-load.php';

use HKS\Auth\Auth;
use HKS\Auth\Token;

header('Content-Type: application/json; charset=UTF-8');

/*
|--------------------------------------------------------------------------
| POSTチェック
|--------------------------------------------------------------------------
*/

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);

    echo json_encode(
        [
            'success'  => false,
            'message'  => '不正なアクセスです。',
            'redirect' => null,
            'data'     => [],
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}

/*
|--------------------------------------------------------------------------
| CSRFチェック
|--------------------------------------------------------------------------
*/

$token = new Token();

$csrfToken = isset($_POST['csrf_token'])
    ? trim((string) $_POST['csrf_token'])
    : '';

if (!$token->verify($csrfToken)) {
    http_response_code(403);

    echo json_encode(
        [
            'success'  => false,
            'message'  => 'CSRFトークンが無効です。',
            'redirect' => null,
            'data'     => [],
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}

/*
|--------------------------------------------------------------------------
| 入力値取得
|--------------------------------------------------------------------------
*/

$forgotPasswordData = [
    'email' => isset($_POST['email'])
        ? sanitize_email(wp_unslash($_POST['email']))
        : '',
];

/*
|--------------------------------------------------------------------------
| パスワード再設定メール送信
|--------------------------------------------------------------------------
*/

$auth = new Auth();

$result = $auth->forgotPassword($forgotPasswordData);

/*
|--------------------------------------------------------------------------
| 処理失敗
|--------------------------------------------------------------------------
*/

if (
    !is_array($result) ||
    !isset($result['success']) ||
    $result['success'] !== true
) {
    http_response_code(400);

    echo json_encode(
        [
            'success' => false,
            'message' => is_array($result)
                ? ($result['message'] ?? 'パスワード再設定処理に失敗しました。')
                : 'パスワード再設定処理に失敗しました。',
            'redirect' => null,
            'data' => is_array($result)
                ? ($result['errors'] ?? [])
                : [],
        ],
        JSON_UNESCAPED_UNICODE
    );

    exit;
}

/*
|--------------------------------------------------------------------------
| 処理成功
|--------------------------------------------------------------------------
*/

echo json_encode(
    [
        'success'  => true,
        'message'  => $result['message'],
        'redirect' => null,
        'data'     => [
            'mail_file' => $result['mail_file'] ?? null,
        ],
    ],
    JSON_UNESCAPED_UNICODE
);

exit;