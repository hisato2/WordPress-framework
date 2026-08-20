<?php

declare(strict_types=1);

namespace HKS\Database\Migrations;

/**
 * hks_products ソフトウェアバージョンカラム追加Migration
 *
 * ソフトウェア商品のバージョン番号を管理する。
 *
 * 例:
 * - 1.0.0
 * - 1.0.1
 * - 1.1.0
 * - 2.0.0
 *
 * software以外の商品ではNULLを使用する。
 */
final class AddSoftwareVersionToProducts
{
    /**
     * Migrationを実行する。
     */
    public static function up(): void
    {
        global $wpdb;

        $tableName = 'hks_products';

        /*
        |--------------------------------------------------------------------------
        | software_version
        |--------------------------------------------------------------------------
        */

        $existingColumn = $wpdb->get_var(
            $wpdb->prepare(
                "
                SHOW COLUMNS
                FROM `{$tableName}`
                LIKE %s
                ",
                'software_version'
            )
        );

        if ($existingColumn === null) {

            $result = $wpdb->query(
                "
                ALTER TABLE `{$tableName}`
                ADD COLUMN `software_version`
                    varchar(50) DEFAULT NULL
                    COMMENT 'ソフトウェアバージョン'
                    AFTER `issue_number`
                "
            );

            if ($result === false) {
                throw new \RuntimeException(
                    'software_versionカラムの追加に失敗しました。'
                );
            }
        }
    }
}