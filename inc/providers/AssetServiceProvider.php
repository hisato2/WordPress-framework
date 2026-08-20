<?php

declare(strict_types=1);

namespace HKS\Providers;


/**
 * WordPress Asset Service Provider
 *
 * CSS・JavaScriptのWordPress登録を担当する。
 */
final class AssetServiceProvider
{
    /**
     * WordPress Hookを登録する。
     */
    public static function register(): void
    {
        add_action(
            'wp_enqueue_scripts',
            [self::class, 'enqueue']
        );
    }


    /**
     * CSS・JavaScriptを読み込む。
     */
    public static function enqueue(): void
    {
        self::enqueueThemeStyle();
        self::enqueueCommonStyles();
        self::enqueuePageStyles();

        self::enqueueCommonScripts();
        self::enqueueJavaScriptConfig();

        self::enqueuePageScripts();
    }


    /**
     * WordPressテーマの基本スタイルを読み込む。
     */
    private static function enqueueThemeStyle(): void
    {
        wp_enqueue_style(
            'hakuhousha-theme',
            get_stylesheet_uri(),
            [],
            self::themeVersion()
        );
    }


    /**
     * 全画面共通CSSを読み込む。
     */
    private static function enqueueCommonStyles(): void
    {
        // Foundation
        self::enqueueStyle(
            'hks-base',
            'css/base.css'
        );

        self::enqueueStyle(
            'hks-layout',
            'css/layout.css'
        );


        // UI Components
        self::enqueueStyle(
            'hks-button',
            'css/button.css'
        );

        self::enqueueStyle(
            'hks-card',
            'css/card.css'
        );

        self::enqueueStyle(
            'hks-form',
            'css/form.css'
        );

        self::enqueueStyle(
            'hks-table',
            'css/table.css'
        );

        self::enqueueStyle(
            'hks-modal',
            'css/modal.css'
        );

        self::enqueueStyle(
            'hks-toast',
            'css/toast.css'
        );


        // Layout Components
        self::enqueueStyle(
            'hks-header',
            'css/header.css'
        );

        self::enqueueStyle(
            'hks-footer',
            'css/footer.css'
        );
    }


    /**
     * ページ別CSSを読み込む。
     */
    private static function enqueuePageStyles(): void
    {
        // Home
        if (is_front_page()) {
            self::enqueueStyle(
                'hks-home',
                'css/home.css'
            );
        }


        // Jobs
        if (is_page('jobs')) {
            self::enqueueStyle(
                'hks-jobs',
                'css/jobs.css'
            );
        }


        // Events
        if (is_page('events')) {
            self::enqueueStyle(
                'hks-events',
                'css/events.css'
            );
        }


        // News
        if (is_page('news')) {
            self::enqueueStyle(
                'hks-news',
                'css/news.css'
            );
        }


        // Contact
        if (is_page('contact')) {
            self::enqueueStyle(
                'hks-contact',
                'css/contact.css'
            );
        }


        // Privacy Policy
        if (is_page('privacy-policy')) {
            self::enqueueStyle(
                'hks-privacy-policy',
                'css/privacy-policy.css'
            );
        }

        // Profile
        if (is_page('profile')) {
            self::enqueueStyle(
                'hks-profile',
                'css/profile.css'
            );
        }


        // Dashboard
        if (is_page('dashboard')) {
            self::enqueueStyle(
                'hks-dashboard',
                'css/dashboard.css'
            );
        }


        // Authentication
        if (
            is_page([
                'login',
                'signup',
                'forgot-password',
                'reset-password',
            ])
        ) {
            self::enqueueStyle(
                'hks-auth',
                'css/auth.css'
            );
        }
    }


    /**
     * 全画面共通JavaScriptを読み込む。
     */
    private static function enqueueCommonScripts(): void
    {
        self::enqueueScript(
            'hks-common',
            'js/common.js',
            [],
            true
        );
    }


    /**
     * JavaScriptへフレームワーク共通設定を渡す。
     *
     * URLをJavaScript側へハードコードしないため、
     * WordPressが現在の環境から生成したURLを渡す。
     */
    private static function enqueueJavaScriptConfig(): void
    {
        wp_localize_script(
            'hks-common',
            'HKS_CONFIG',
            [
                'homeUrl' => home_url('/'),
                'themeUrl' => HKS_THEME_URL,
                'assetUrl' => ASSET_URL,
                'apiUrl' => HKS_API_URL,
                'environment' => wp_get_environment_type(),
            ]
        );
    }


    /**
     * ページ別JavaScriptを読み込む。
     */
    private static function enqueuePageScripts(): void
    {
        // Authentication


        if (is_page('forgot-password')) {
            self::enqueueScript(
                'hks-forgot-password',
                'js/auth/forgot-password.js',
                ['hks-common'],
                true
            );
        }


        if (is_page('reset-password')) {
            self::enqueueScript(
                'hks-reset-password',
                'js/auth/reset-password.js',
                ['hks-common'],
                true
            );
        }


        // Jobs
        if (is_page('jobs')) {
            self::enqueueScript(
                'hks-jobs',
                'js/jobs/jobs.js',
                ['hks-common'],
                true
            );
        }


        // Contact
        if (is_page('contact')) {
            self::enqueueScript(
                'hks-contact',
                'js/contact/contact.js',
                ['hks-common'],
                true
            );
        }


        // Events
        if (is_page('events')) {
            self::enqueueScript(
                'hks-events',
                'js/events/events.js',
                ['hks-common'],
                true
            );
        }


        // My Page
        if (is_page('mypage')) {
            self::enqueueStyle(
                'hks-mypage',
                'css/mypage.css'
            );
        }


        // Profile
        if (is_page('profile')) {
            self::enqueueScript(
                'hks-profile',
                'js/profile/profile.js',
                ['hks-common'],
                true
            );
        }


          // Dashboard
        if (is_page('dashboard')) {

            $view = isset($_GET['view'])
                ? sanitize_key(
                    wp_unslash($_GET['view'])
                )
                : '';


            /*
            |--------------------------------------------------------------------------
            | Product Form
            |--------------------------------------------------------------------------
            */

            if (
                in_array(
                    $view,
                    [
                        'product-create',
                        'product-edit',
                    ],
                    true
                )
            ) {
                self::enqueueScript(
                    'hks-product-form',
                    'js/products/product-form.js',
                    ['hks-common'],
                    true
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Series Form
            |--------------------------------------------------------------------------
            */

            if (
                in_array(
                    $view,
                    [
                        'series-create',
                        'series-edit',
                    ],
                    true
                )
            ) {
                self::enqueueScript(
                    'hks-series-form',
                    'js/series/series-form.js',
                    ['hks-common'],
                    true
                );
            }

        }
        

    }


    /**
     * CSSを登録する。
     *
     * 開発時は更新日時をVersionとして使用する。
     */
    private static function enqueueStyle(
        string $handle,
        string $relativePath,
        array $dependencies = []
    ): void {
        wp_enqueue_style(
            $handle,
            asset($relativePath),
            $dependencies,
            self::assetVersion($relativePath)
        );
    }


    /**
     * JavaScriptを登録する。
     *
     * @param string[] $dependencies
     */
    private static function enqueueScript(
        string $handle,
        string $relativePath,
        array $dependencies = [],
        bool $inFooter = true
    ): void {
        wp_enqueue_script(
            $handle,
            asset($relativePath),
            $dependencies,
            self::assetVersion($relativePath),
            $inFooter
        );
    }


    /**
     * AssetのVersionを取得する。
     */
    private static function assetVersion(
        string $relativePath
    ): string {
        $filePath =
            ASSET_PATH .
            '/' .
            ltrim($relativePath, '/');


        if (is_file($filePath)) {
            $modifiedTime = filemtime($filePath);


            if ($modifiedTime !== false) {
                return (string) $modifiedTime;
            }
        }


        return self::themeVersion();
    }


    /**
     * テーマVersionを取得する。
     */
    private static function themeVersion(): string
    {
        $version = wp_get_theme()->get('Version');


        return is_string($version) && $version !== ''
            ? $version
            : '1.0.0';
    }
}
