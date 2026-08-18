<?php

declare(strict_types=1);

namespace HKS\Database\Migrations;


/**
 * hks_order_items テーブル作成Migration
 *
 * 注文に含まれる商品・販売形態を管理する。
 *
 * 対応例:
 *
 * - 通常単行本の単品購入
 * - 書籍シリーズ各巻の単品購入
 * - 季刊誌各号の単品購入
 * - 月刊誌各号の単品購入
 * - 季刊誌の年間定期購読
 * - 月刊誌の年間定期購読
 * - ソフトウェアライセンス購入
 *
 * 注文時点の商品名・販売価格・税情報を
 * スナップショットとして保存する。
 */
final class CreateOrderItemsTable
{
    /**
     * Migrationを実行する。
     */
    public static function up(): void
    {
        global $wpdb;


        $tableName = 'hks_order_items';


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


                /*
                |--------------------------------------------------------------------------
                | Relations
                |--------------------------------------------------------------------------
                */


                `order_id` bigint(20) unsigned NOT NULL
                    COMMENT 'hks_orders.id',

                `sales_option_id` bigint(20) unsigned NOT NULL
                    COMMENT 'hks_sales_options.id',

                `product_id` bigint(20) unsigned DEFAULT NULL
                    COMMENT '単品商品・ソフトウェア等のhks_products.id',

                `series_id` bigint(20) unsigned DEFAULT NULL
                    COMMENT '定期購読対象のhks_product_series.id',


                /*
                |--------------------------------------------------------------------------
                | Item Snapshot
                |--------------------------------------------------------------------------
                |
                | 商品マスターや販売設定が後日変更されても、
                | 注文時点の内容を保持する。
                |
                */


                `product_code` varchar(50) DEFAULT NULL
                    COMMENT '注文時の商品コード',

                `item_name` varchar(255) NOT NULL
                    COMMENT '注文時の商品・販売形態名称',

                `sales_type` enum(
                    'single',
                    'subscription'
                ) NOT NULL
                    COMMENT '注文時の販売形態',


                /*
                |--------------------------------------------------------------------------
                | Quantity / Price
                |--------------------------------------------------------------------------
                */


                `quantity` int unsigned NOT NULL DEFAULT 1
                    COMMENT '購入数量',

                `unit_price` int unsigned NOT NULL DEFAULT 0
                    COMMENT '注文時の商品単価',

                `subtotal` int unsigned NOT NULL DEFAULT 0
                    COMMENT '数量×単価の小計',


                /*
                |--------------------------------------------------------------------------
                | Tax Snapshot
                |--------------------------------------------------------------------------
                */


                `tax_rate` decimal(5,2) NOT NULL DEFAULT 10.00
                    COMMENT '注文時の消費税率',

                `tax_type` enum(
                    'included',
                    'excluded',
                    'exempt'
                ) NOT NULL DEFAULT 'included'
                    COMMENT '注文時の税区分',

                `tax_amount` int unsigned NOT NULL DEFAULT 0
                    COMMENT 'この明細に含まれる消費税額',


                /*
                |--------------------------------------------------------------------------
                | Subscription Snapshot
                |--------------------------------------------------------------------------
                |
                | 定期購読の場合、
                | 購入時点の購読条件を保存する。
                |
                */


                `subscription_months` smallint unsigned DEFAULT NULL
                    COMMENT '購読期間（月数）',

                `scheduled_shipments` smallint unsigned DEFAULT NULL
                    COMMENT '予定発送回数',


                /*
                |--------------------------------------------------------------------------
                | Timestamps
                |--------------------------------------------------------------------------
                */


                `created_at` datetime DEFAULT CURRENT_TIMESTAMP,

                `updated_at` datetime DEFAULT CURRENT_TIMESTAMP
                    ON UPDATE CURRENT_TIMESTAMP,


                /*
                |--------------------------------------------------------------------------
                | Keys
                |--------------------------------------------------------------------------
                */


                PRIMARY KEY (`id`),

                KEY `idx_order_id` (`order_id`),

                KEY `idx_sales_option_id` (`sales_option_id`),

                KEY `idx_product_id` (`product_id`),

                KEY `idx_series_id` (`series_id`),

                KEY `idx_sales_type` (`sales_type`),


                /*
                |--------------------------------------------------------------------------
                | Foreign Keys
                |--------------------------------------------------------------------------
                */


                CONSTRAINT `fk_hks_order_items_order`
                    FOREIGN KEY (`order_id`)
                    REFERENCES `hks_orders` (`id`)
                    ON UPDATE CASCADE
                    ON DELETE CASCADE,

                CONSTRAINT `fk_hks_order_items_sales_option`
                    FOREIGN KEY (`sales_option_id`)
                    REFERENCES `hks_sales_options` (`id`)
                    ON UPDATE CASCADE
                    ON DELETE RESTRICT,

                CONSTRAINT `fk_hks_order_items_product`
                    FOREIGN KEY (`product_id`)
                    REFERENCES `hks_products` (`id`)
                    ON UPDATE CASCADE
                    ON DELETE SET NULL,

                CONSTRAINT `fk_hks_order_items_series`
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
                'hks_order_itemsテーブルの作成に失敗しました。'
            );
        }
    }
}