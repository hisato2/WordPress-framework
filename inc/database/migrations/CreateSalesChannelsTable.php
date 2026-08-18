<?php

declare(strict_types=1);

namespace HKS\Database\Migrations;


/**
 * hks_sales_channels テーブル作成Migration
 *
 * 販売形態ごとの販売チャネルを管理する。
 *
 * 例:
 *
 * 単行本 単品購入
 *   ├─ Amazon
 *   └─ Stripe
 *
 * 季刊誌 年間定期購読
 *   ├─ Amazon
 *   └─ Stripe
 *
 * 月刊会報 年間定期購読
 *   ├─ Amazon
 *   └─ Stripe
 */
final class CreateSalesChannelsTable
{
    /**
     * Migrationを実行する。
     */
    public static function up(): void
    {
        global $wpdb;


        $tableName = 'hks_sales_channels';


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

                `sales_option_id` bigint(20) unsigned NOT NULL
                    COMMENT 'hks_sales_options.id',

                `channel` enum(
                    'amazon',
                    'stripe'
                ) NOT NULL
                    COMMENT '販売チャネル',

                `external_product_id` varchar(255) DEFAULT NULL
                    COMMENT 'Amazon ASIN または Stripe Product ID',

                `external_price_id` varchar(255) DEFAULT NULL
                    COMMENT 'Stripe Price ID等',

                `external_sku` varchar(255) DEFAULT NULL
                    COMMENT 'Amazon Seller SKU等',

                `external_url` text DEFAULT NULL
                    COMMENT 'Amazon商品ページ等の外部URL',

                `enabled` tinyint(1) NOT NULL DEFAULT 1
                    COMMENT '販売チャネル有効・無効',

                `created_at` datetime DEFAULT CURRENT_TIMESTAMP,

                `updated_at` datetime DEFAULT CURRENT_TIMESTAMP
                    ON UPDATE CURRENT_TIMESTAMP,

                PRIMARY KEY (`id`),

                UNIQUE KEY `uk_sales_option_channel` (
                    `sales_option_id`,
                    `channel`
                ),

                KEY `idx_channel` (`channel`),

                KEY `idx_external_product_id` (`external_product_id`),

                KEY `idx_external_sku` (`external_sku`),

                CONSTRAINT `fk_hks_sales_channels_option`
                    FOREIGN KEY (`sales_option_id`)
                    REFERENCES `hks_sales_options` (`id`)
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
                'hks_sales_channelsテーブルの作成に失敗しました。'
            );
        }
    }
}