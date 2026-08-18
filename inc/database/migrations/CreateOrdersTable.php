<?php

declare(strict_types=1);

namespace HKS\Database\Migrations;


/**
 * hks_orders テーブル作成Migration
 *
 * 注文の基本情報を管理する。
 *
 * 対応する販売チャネル:
 * - Amazon
 * - Stripe
 *
 * 注文時点の金額・購入者情報・発送先情報を
 * スナップショットとして保存する。
 *
 * 商品の詳細は hks_order_items で管理する。
 * 実際の発送情報は hks_shipments で管理する。
 */
final class CreateOrdersTable
{
    /**
     * Migrationを実行する。
     */
    public static function up(): void
    {
        global $wpdb;


        $tableName = 'hks_orders';


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

                `order_number` varchar(50) NOT NULL
                    COMMENT '注文番号',

                `user_id` bigint(20) unsigned DEFAULT NULL
                    COMMENT 'hks_users.id',

                `sales_channel` enum(
                    'amazon',
                    'stripe'
                ) NOT NULL
                    COMMENT '販売チャネル',

                `external_order_id` varchar(255) DEFAULT NULL
                    COMMENT 'Amazon等の外部注文番号',

                `external_payment_id` varchar(255) DEFAULT NULL
                    COMMENT 'Stripe PaymentIntent等の決済ID',

                /*
                |--------------------------------------------------------------------------
                | Amount
                |--------------------------------------------------------------------------
                */

                `subtotal` int unsigned NOT NULL DEFAULT 0
                    COMMENT '商品小計',

                `shipping_amount` int unsigned NOT NULL DEFAULT 0
                    COMMENT '購入者へ請求した送料',

                `tax_amount` int unsigned NOT NULL DEFAULT 0
                    COMMENT '消費税額',

                `discount_amount` int unsigned NOT NULL DEFAULT 0
                    COMMENT '割引額',

                `total_amount` int unsigned NOT NULL DEFAULT 0
                    COMMENT '注文合計金額',

                `currency` char(3) NOT NULL DEFAULT 'JPY'
                    COMMENT '通貨コード',

                /*
                |--------------------------------------------------------------------------
                | Payment
                |--------------------------------------------------------------------------
                */

                `payment_status` enum(
                    'pending',
                    'paid',
                    'failed',
                    'refunded',
                    'partially_refunded',
                    'cancelled'
                ) NOT NULL DEFAULT 'pending'
                    COMMENT '決済状態',

                `paid_at` datetime DEFAULT NULL
                    COMMENT '入金・決済完了日時',

                /*
                |--------------------------------------------------------------------------
                | Order Status
                |--------------------------------------------------------------------------
                */

                `order_status` enum(
                    'pending',
                    'confirmed',
                    'processing',
                    'completed',
                    'cancelled'
                ) NOT NULL DEFAULT 'pending'
                    COMMENT '注文処理状態',

                /*
                |--------------------------------------------------------------------------
                | Customer Snapshot
                |--------------------------------------------------------------------------
                |
                | 会員情報が後日変更されても、
                | 注文時点の購入者情報を保持する。
                |
                */

                `customer_email` varchar(255) DEFAULT NULL
                    COMMENT '注文時メールアドレス',

                `customer_last_name` varchar(100) DEFAULT NULL
                    COMMENT '注文時姓',

                `customer_first_name` varchar(100) DEFAULT NULL
                    COMMENT '注文時名',

                `customer_phone` varchar(30) DEFAULT NULL
                    COMMENT '注文時電話番号',

                /*
                |--------------------------------------------------------------------------
                | Shipping Address Snapshot
                |--------------------------------------------------------------------------
                */

                `shipping_last_name` varchar(100) DEFAULT NULL
                    COMMENT '発送先姓',

                `shipping_first_name` varchar(100) DEFAULT NULL
                    COMMENT '発送先名',

                `shipping_postal_code` varchar(20) DEFAULT NULL
                    COMMENT '発送先郵便番号',

                `shipping_prefecture` varchar(100) DEFAULT NULL
                    COMMENT '発送先都道府県',

                `shipping_city` varchar(100) DEFAULT NULL
                    COMMENT '発送先市区町村',

                `shipping_address1` varchar(255) DEFAULT NULL
                    COMMENT '発送先住所1',

                `shipping_address2` varchar(255) DEFAULT NULL
                    COMMENT '発送先住所2',

                `shipping_phone` varchar(30) DEFAULT NULL
                    COMMENT '発送先電話番号',

                /*
                |--------------------------------------------------------------------------
                | Notes
                |--------------------------------------------------------------------------
                */

                `customer_note` text DEFAULT NULL
                    COMMENT '購入者からの備考',

                `admin_note` text DEFAULT NULL
                    COMMENT '管理者用備考',

                /*
                |--------------------------------------------------------------------------
                | Timestamps
                |--------------------------------------------------------------------------
                */

                `ordered_at` datetime DEFAULT CURRENT_TIMESTAMP
                    COMMENT '注文日時',

                `created_at` datetime DEFAULT CURRENT_TIMESTAMP,

                `updated_at` datetime DEFAULT CURRENT_TIMESTAMP
                    ON UPDATE CURRENT_TIMESTAMP,

                /*
                |--------------------------------------------------------------------------
                | Keys
                |--------------------------------------------------------------------------
                */

                PRIMARY KEY (`id`),

                UNIQUE KEY `uk_order_number` (`order_number`),

                KEY `idx_user_id` (`user_id`),

                KEY `idx_sales_channel` (`sales_channel`),

                KEY `idx_external_order_id` (`external_order_id`),

                KEY `idx_external_payment_id` (`external_payment_id`),

                KEY `idx_payment_status` (`payment_status`),

                KEY `idx_order_status` (`order_status`),

                KEY `idx_ordered_at` (`ordered_at`),

                CONSTRAINT `fk_hks_orders_user`
                    FOREIGN KEY (`user_id`)
                    REFERENCES `hks_users` (`id`)
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
                'hks_ordersテーブルの作成に失敗しました。'
            );
        }
    }
}