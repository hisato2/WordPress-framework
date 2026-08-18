<?php

declare(strict_types=1);

namespace HKS\Setup;

use HKS\Database\DatabaseManager;

/**
 * Framework Installer
 *
 * Frameworkの初期インストールおよび
 * Database Migrationを管理する。
 */
final class Installer
{
    /**
     * Framework Version
     */
    private const VERSION = '1.0.0';

    /**
     * Database Schema Version
     *
     * Migrationを追加・変更した場合に更新する。
     */
    private const DB_VERSION = '1.0.0';

    /**
     * 初回インストール
     *
     * テーマ有効化時に実行する。
     */
    public static function install(): void
    {
        DatabaseManager::migrate();

        update_option(
            'hks_framework_db_version',
            self::DB_VERSION
        );

        if (!self::isInstalled()) {
            update_option(
                'hks_framework_installed',
                '1'
            );

            update_option(
                'hks_framework_installed_at',
                current_time('mysql')
            );
        }

        update_option(
            'hks_framework_version',
            self::VERSION
        );
    }

    /**
     * Framework更新確認
     *
     * 通常アクセス時に呼び出されるが、
     * DB Versionが同じ場合は何もしない。
     */
    public static function maybeUpgrade(): void
    {
        if (!self::isInstalled()) {
            return;
        }

        $installedVersion = (string) get_option(
            'hks_framework_db_version',
            '0.0.0'
        );

        if (
            version_compare(
                $installedVersion,
                self::DB_VERSION,
                '>='
            )
        ) {
            return;
        }

        self::upgrade();
    }

    /**
     * Framework Upgrade
     */
    private static function upgrade(): void
    {
        DatabaseManager::migrate();

        update_option(
            'hks_framework_db_version',
            self::DB_VERSION
        );

        update_option(
            'hks_framework_version',
            self::VERSION
        );
    }

    /**
     * Frameworkがインストール済みか確認する。
     */
    public static function isInstalled(): bool
    {
        return get_option(
            'hks_framework_installed',
            '0'
        ) === '1';
    }

    /**
     * Framework Version取得
     */
    public static function getVersion(): string
    {
        return self::VERSION;
    }

    /**
     * Database Version取得
     */
    public static function getDatabaseVersion(): string
    {
        return self::DB_VERSION;
    }
}