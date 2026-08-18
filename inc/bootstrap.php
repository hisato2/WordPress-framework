<?php

declare(strict_types=1);

/**
 * Hakuhousha Framework Bootstrap
 *
 * Framework共通定数、Autoload、Providerを初期化する。
 *
 * @package HakuhoushaPortfolio
 */

if (!defined('ABSPATH')) {
    exit;
}

/*
|--------------------------------------------------------------------------
| Framework Paths
|--------------------------------------------------------------------------
*/

if (!defined('HKS_THEME_PATH')) {
    define('HKS_THEME_PATH', get_template_directory());
}

if (!defined('HKS_THEME_URL')) {
    define('HKS_THEME_URL', get_template_directory_uri());
}

if (!defined('VIEW_PATH')) {
    define('VIEW_PATH', HKS_THEME_PATH . '/views');
}

if (!defined('ASSET_PATH')) {
    define('ASSET_PATH', HKS_THEME_PATH . '/assets');
}

if (!defined('ASSET_URL')) {
    define('ASSET_URL', HKS_THEME_URL . '/assets');
}

if (!defined('HKS_API_PATH')) {
    define('HKS_API_PATH', HKS_THEME_PATH . '/api');
}

if (!defined('HKS_API_URL')) {
    define('HKS_API_URL', HKS_THEME_URL . '/api');
}



/*
|--------------------------------------------------------------------------
| Autoloader
|--------------------------------------------------------------------------
|
| HKS\Auth\Session
| ↓
| inc/auth/Session.php
|
| HKS\Providers\AssetServiceProvider
| ↓
| inc/providers/AssetServiceProvider.php
|
*/

spl_autoload_register(
    static function (string $class): void {
        $prefix = 'HKS\\';

        if (strpos($class, $prefix) !== 0) {
            return;
        }

        $relativeClass = substr($class, strlen($prefix));

        if ($relativeClass === '') {
            return;
        }

        $parts = explode('\\', $relativeClass);
        $className = array_pop($parts);

        $directory = HKS_THEME_PATH . '/inc';

        if ($parts !== []) {
            $directory .= '/' . strtolower(implode('/', $parts));
        }

        $file = $directory . '/' . $className . '.php';

        if (is_file($file)) {
            require_once $file;
        }
    }
);

/*
|--------------------------------------------------------------------------
| Framework Helpers
|--------------------------------------------------------------------------
*/

/**
 * assetsディレクトリ内のURLを生成する。
 *
 * DBへURLを保存せず、現在の実行環境からURLを生成する。
 */
if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return ASSET_URL . '/' . ltrim($path, '/');
    }
}

/*
|--------------------------------------------------------------------------
| Service Providers
|--------------------------------------------------------------------------
*/

\HKS\Providers\AssetServiceProvider::register();
\HKS\Providers\RouteServiceProvider::register();
\HKS\Providers\SetupServiceProvider::register();