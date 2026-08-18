<?php

declare(strict_types=1);

namespace HKS\Database\Migrations;


/**
 * hks_sales_options テーブル作成Migration
 *
 * 商品・シリーズの販売形態を管理する。
 *
 * 販売形態:
 *
 * single
 *   単品購入
 *
 * subscription
 *   定期購読
 *
 * 例:
 *
 * 単行本
 *   → product_id を指定
 *   → single
 *
 * 書籍シリーズ各巻
 *   → product_id を指定
 *   → single
 *
 * 季刊誌各号
 *   → product_id を指定
 *   → single
 *
 * 季刊誌年間購読
 *   → series_id を指定
 *   → subscription
 *
 * 月刊会報各号
 *   → product_id を指定
 *   → single
 *
 * 月刊会報年間購読
 *   → series_id を指定
 *   → subscription
 */
final class CreateSalesOptionsTable
{
    /**
     * Migrationを実行する。
     */
    public static function up(): void
    {
        global $wpdb;


        $tableName = 'hks_sales_options';


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

                `product_id` bigint(20) unsigned DEFAULT NULL
                    COMMENT '単品販売の場合のhks_products.id',

                `series_id` bigint(20) unsigned DEFAULT NULL
                    COMMENT '定期購読の場合のhks_product_series.id',

                `sales_type` enum(
                    'single',
                    'subscription'
                ) NOT NULL
                    COMMENT '販売形態',

                `name` varchar(255) NOT NULL
                    COMMENT '販売形態の表示名',

                `price` int unsigned NOT NULL
                    COMMENT '販売価格',

                `subscription_months` smallint unsigned DEFAULT NULL
                    COMMENT '購読期間（月数）',

                `scheduled_shipments` smallint unsigned DEFAULT NULL
                    COMMENT '購読期間中の予定発送回数',

                `shipping_policy` enum(
                    'per_order',
                    'included',
                    'per_shipment',
                    'annual_fixed',
                    'free'
                ) NOT NULL DEFAULT 'per_order'
                    COMMENT '送料計算方式',

                `shipping_amount` int unsigned NOT NULL DEFAULT 0
                    COMMENT '送料設定額',

                `status` enum(
                    'active',
                    'inactive'
                ) NOT NULL DEFAULT 'active'
                    COMMENT '販売状態',

                `created_at` datetime DEFAULT CURRENT_TIMESTAMP,

                `updated_at` datetime DEFAULT CURRENT_TIMESTAMP
                    ON UPDATE CURRENT_TIMESTAMP,

                PRIMARY KEY (`id`),

                KEY `idx_product_id` (`product_id`),

                KEY `idx_series_id` (`series_id`),

                KEY `idx_sales_type` (`sales_type`),

                KEY `idx_status` (`status`),

            CONSTRAINT `fk_hks_sales_options_product`
                FOREIGN KEY (`product_id`)
                REFERENCES `hks_products` (`id`)
                ON UPDATE CASCADE
                ON DELETE RESTRICT,

            CONSTRAINT `fk_hks_sales_options_series`
                FOREIGN KEY (`series_id`)
                REFERENCES `hks_product_series` (`id`)
                ON UPDATE CASCADE
                ON DELETE RESTRICT

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
                'hks_sales_optionsテーブルの作成に失敗しました。'
            );
        }
    }
}