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

$resetPasswordData = [
    'token' => isset($_POST['token'])
        ? sanitize_text_field(wp_unslash($_POST['token']))
        : '',

    'password' => isset($_POST['password'])
        ? (string) wp_unslash($_POST['password'])
        : '',

    'password_confirmation' => isset($_POST['password_confirmation'])
        ? (string) wp_unslash($_POST['password_confirmation'])
        : '',
];

/*
|--------------------------------------------------------------------------
| パスワード再設定
|--------------------------------------------------------------------------
*/

$auth = new Auth();

$result = $auth->resetPassword(
    $resetPasswordData
);

/*
|--------------------------------------------------------------------------
| 処理失敗
|--------------------------------------------------------------------------
*/

if (
    !isset($result['success']) ||
    $result['success'] !== true
) {
    http_response_code(400);

    echo json_encode(
        [
            'success' => false,
            'message' => $result['message']
                ?? 'パスワードの変更に失敗しました。',
            'redirect' => null,
            'data' => [
                'errors' => $result['errors'] ?? [],
            ],
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
        'redirect' => home_url('/login/?reset=success'),
        'data'     => [],
    ],
    JSON_UNESCAPED_UNICODE
);

exit;