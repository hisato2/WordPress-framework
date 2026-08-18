<?php

declare(strict_types=1);

namespace HKS\Providers;

use HKS\Setup\Installer;

/**
 * Framework Setup Service Provider
 *
 * Frameworkの初期インストール・更新確認を登録する。
 */
final class SetupServiceProvider
{
    /**
     * WordPress Hookを登録する。
     */
    public static function register(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Initial Installation
        |--------------------------------------------------------------------------
        |
        | テーマ有効化時にFrameworkを初期セットアップする。
        |
        */

        add_action(
            'after_switch_theme',
            [Installer::class, 'install']
        );

        /*
        |--------------------------------------------------------------------------
        | Framework Upgrade Check
        |--------------------------------------------------------------------------
        |
        | 通常起動時はDB Versionだけ確認し、
        | 更新が必要な場合のみMigrationを実行する。
        |
        */

        add_action(
            'init',
            [Installer::class, 'maybeUpgrade'],
            5
        );
    }
}