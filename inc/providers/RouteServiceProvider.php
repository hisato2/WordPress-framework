<?php

declare(strict_types=1);

namespace HKS\Providers;

/**
 * WordPress Route Service Provider
 *
 * WordPress HookとFramework APIの接続を担当する。
 *
 * API内部の業務処理は担当しない。
 */
final class RouteServiceProvider
{
    /**
     * Route定義を読み込み、WordPress Hookへ登録する。
     */
    public static function register(): void
    {
        $routesFile = HKS_THEME_PATH . '/inc/routes.php';

        if (!is_file($routesFile)) {
            return;
        }

        $routes = require $routesFile;

        if (!is_array($routes)) {
            return;
        }

        foreach ($routes as $route) {
            self::registerRoute($route);
        }
    }

    /**
     * Routeを1件登録する。
     *
     * @param array{
     *     action?: string,
     *     file?: string,
     *     public?: bool
     * } $route
     */
    private static function registerRoute(array $route): void
    {
        $action = isset($route['action'])
            ? trim((string) $route['action'])
            : '';

        $file = isset($route['file'])
            ? trim((string) $route['file'])
            : '';

        $isPublic = isset($route['public'])
            ? (bool) $route['public']
            : false;

        if ($action === '' || $file === '') {
            return;
        }

        $handler = static function () use ($file): void {
            self::dispatch($file);
        };

        add_action(
            'admin_post_' . $action,
            $handler
        );

        if ($isPublic) {
            add_action(
                'admin_post_nopriv_' . $action,
                $handler
            );
        }
    }

    /**
     * APIファイルへ処理を振り分ける。
     */
    private static function dispatch(string $relativeFile): void
    {
        $normalizedFile = ltrim($relativeFile, '/');
        $apiFile = HKS_API_PATH . '/' . $normalizedFile;

        if (!is_file($apiFile)) {
            self::sendNotFoundResponse();
        }

        require $apiFile;
    }

    /**
     * APIファイルが存在しない場合の応答。
     */
    private static function sendNotFoundResponse(): void
    {
        if (!headers_sent()) {
            status_header(404);
            header('Content-Type: application/json; charset=utf-8');
        }

        echo wp_json_encode(
            [
                'success' => false,
                'message' => 'API endpoint was not found.',
            ],
            JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
        );

        exit;
    }
}