<?php

declare(strict_types=1);

namespace HKS\Database\Migrations;


/**
 * hks_product_series テーブル作成Migration
 *
 * 商品シリーズ・刊行物シリーズを管理する。
 *
 * 例:
 * - ○○著作集
 * - ○○季刊誌
 * - 月刊会報
 *
 * シリーズを持たない通常の単行本は
 * このテーブルには登録せず、
 * hks_products.series_id を NULL とする。
 */
final class CreateProductSeriesTable
{
    /**
     * Migrationを実行する。
     */
    public static function up(): void
    {
        global $wpdb;


        $tableName = 'hks_product_series';


        /*
        |--------------------------------------------------------------------------
        | 既存テーブル確認
        |--------------------------------------------------------------------------
        */


        $existingTable = $wpdb->get_var(
            $wpdb->prepare(
                'SHOW TABLES LIKE %s',
                $tableName
            )
        );


        if ($existingTable === $tableName) {
            return;
        }


        /*
        |--------------------------------------------------------------------------
        | Table Creation
        |--------------------------------------------------------------------------
        */


        $sql = "
            CREATE TABLE `{$tableName}` (

                `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,

                `series_code` varchar(50) NOT NULL
                    COMMENT 'シリーズ内部コード',

                `series_type` enum(
                    'book_series',
                    'quarterly',
                    'monthly'
                ) NOT NULL
                    COMMENT 'シリーズ種別',

                `name` varchar(255) NOT NULL
                    COMMENT 'シリーズ名',

                `description` text DEFAULT NULL
                    COMMENT 'シリーズ説明',

                `issn` varchar(20) DEFAULT NULL
                    COMMENT 'ISSN',

                `status` enum(
                    'draft',
                    'active',
                    'inactive',
                    'discontinued'
                ) NOT NULL DEFAULT 'draft'
                    COMMENT 'シリーズ状態',

                `created_at` datetime DEFAULT CURRENT_TIMESTAMP,

                `updated_at` datetime DEFAULT CURRENT_TIMESTAMP
                    ON UPDATE CURRENT_TIMESTAMP,

                PRIMARY KEY (`id`),

                UNIQUE KEY `uk_series_code` (`series_code`),

                KEY `idx_series_type` (`series_type`),

                KEY `idx_issn` (`issn`),

                KEY `idx_status` (`status`)

            )
            ENGINE=InnoDB
            DEFAULT CHARSET=utf8mb4
            COLLATE=utf8mb4_unicode_ci
        ";


        $result = $wpdb->query($sql);


        /*
        |--------------------------------------------------------------------------
        | Error Handling
        |--------------------------------------------------------------------------
        */


        if ($result === false) {
            throw new \RuntimeException(
                'hks_product_seriesテーブルの作成に失敗しました。'
            );
        }
    }
}