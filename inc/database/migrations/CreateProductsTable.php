<?php

declare(strict_types=1);

namespace HKS\Database\Migrations;


/**
 * hks_products テーブル作成Migration
 *
 * 実際に販売・在庫管理する商品を管理する。
 *
 * 例:
 *
 * シリーズなし:
 * - 放射線技術学入門
 *
 * 書籍シリーズ:
 * - ○○著作集 第1巻
 * - ○○著作集 第2巻
 *
 * 季刊誌:
 * - ○○季刊誌 2026年春号
 * - ○○季刊誌 2026年夏号
 *
 * 月刊誌:
 * - 月刊会報 2026年8月号
 */
final class CreateProductsTable
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

                `series_id` bigint(20) unsigned DEFAULT NULL
                    COMMENT 'hks_product_series.id',

                `product_code` varchar(50) NOT NULL
                    COMMENT '商品内部コード',

                `product_type` enum(
                    'book',
                    'volume',
                    'issue',
                    'software'
                ) NOT NULL
                    COMMENT '商品種別',

                `name` varchar(255) NOT NULL
                    COMMENT '商品名',

                `description` text DEFAULT NULL
                    COMMENT '商品説明',

                `isbn` varchar(20) DEFAULT NULL
                    COMMENT 'ISBN',

                `volume_number` varchar(50) DEFAULT NULL
                    COMMENT '書籍シリーズの巻番号',

                `issue_number` varchar(50) DEFAULT NULL
                    COMMENT '雑誌等の号番号',

                `publication_year` smallint unsigned DEFAULT NULL
                    COMMENT '発行年',

                `publication_month` tinyint unsigned DEFAULT NULL
                    COMMENT '発行月',

                `publication_date` date DEFAULT NULL
                    COMMENT '発行日',

                `image_path` varchar(255) DEFAULT NULL
                    COMMENT '商品画像パス',

                `tax_rate` decimal(5,2) NOT NULL DEFAULT 10.00
                    COMMENT '消費税率',

                `tax_type` enum(
                    'included',
                    'excluded',
                    'exempt'
                ) NOT NULL DEFAULT 'included'
                    COMMENT '税込・税抜・非課税',

                `status` enum(
                    'draft',
                    'active',
                    'inactive',
                    'discontinued'
                ) NOT NULL DEFAULT 'draft'
                    COMMENT '商品状態',

                `created_at` datetime DEFAULT CURRENT_TIMESTAMP,

                `updated_at` datetime DEFAULT CURRENT_TIMESTAMP
                    ON UPDATE CURRENT_TIMESTAMP,

                PRIMARY KEY (`id`),

                UNIQUE KEY `uk_product_code` (`product_code`),

                KEY `idx_series_id` (`series_id`),

                KEY `idx_product_type` (`product_type`),

                KEY `idx_isbn` (`isbn`),

                KEY `idx_publication_date` (`publication_date`),

                KEY `idx_status` (`status`),

                CONSTRAINT `fk_hks_products_series`
                    FOREIGN KEY (`series_id`)
                    REFERENCES `hks_product_series` (`id`)
                    ON UPDATE CASCADE
                    ON DELETE SET NULL

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
                'hks_productsテーブルの作成に失敗しました。'
            );
        }
    }
}