<?php

declare(strict_types=1);

namespace HKS\Database\Migrations;


/**
 * hks_shipments テーブル作成Migration
 *
 * 注文・定期購読に対する実際の発送情報を管理する。
 *
 * 対応例:
 *
 * - 通常商品の単品発送
 * - 書籍シリーズ各巻の発送
 * - 季刊誌各号の発送
 * - 月刊誌各号の発送
 * - 定期購読の第1回〜第12回発送
 *
 * 定期購読の場合は hks_subscriptions を参照し、
 * 各発送回ごとに1レコードを作成する。
 *
 * 実際に発送する商品・号は
 * hks_products.id で管理する。
 */
final class CreateShipmentsTable
{
    /**
     * Migrationを実行する。
     */
    public static function up(): void
    {
        global $wpdb;


        $tableName = 'hks_shipments';


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
                    COMMENT '発送元となるhks_orders.id',

                `subscription_id` bigint(20) unsigned DEFAULT NULL
                    COMMENT '定期購読発送の場合のhks_subscriptions.id',

                `product_id` bigint(20) unsigned DEFAULT NULL
                    COMMENT '実際に発送するhks_products.id',


                /*
                |--------------------------------------------------------------------------
                | Shipment Schedule
                |--------------------------------------------------------------------------
                */


                `shipment_number` smallint unsigned DEFAULT NULL
                    COMMENT '定期購読の第何回目の発送か',

                `scheduled_ship_date` date DEFAULT NULL
                    COMMENT '発送予定日',

                `shipped_at` datetime DEFAULT NULL
                    COMMENT '実際の発送日時',


                /*
                |--------------------------------------------------------------------------
                | Shipping Status
                |--------------------------------------------------------------------------
                */


                `status` enum(
                    'pending',
                    'ready',
                    'shipped',
                    'delivered',
                    'cancelled'
                ) NOT NULL DEFAULT 'pending'
                    COMMENT '発送状態',


                /*
                |--------------------------------------------------------------------------
                | Shipping Method / Tracking
                |--------------------------------------------------------------------------
                */


                `shipping_method_id` bigint(20) unsigned DEFAULT NULL
                    COMMENT 'hks_shipping_methods.id',

                `carrier_name` varchar(100) DEFAULT NULL
                    COMMENT '配送会社名',

                `tracking_number` varchar(255) DEFAULT NULL
                    COMMENT '追跡番号',


                /*
                |--------------------------------------------------------------------------
                | Quantity
                |--------------------------------------------------------------------------
                */


                `quantity` int unsigned NOT NULL DEFAULT 1
                    COMMENT '今回発送する数量',


                /*
                |--------------------------------------------------------------------------
                | Shipping Address Snapshot
                |--------------------------------------------------------------------------
                |
                | 会員情報や注文情報が後から変更されても、
                | 実際にどこへ発送したかを保持する。
                |
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


                `admin_note` text DEFAULT NULL
                    COMMENT '発送管理用備考',


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

                KEY `idx_subscription_id` (`subscription_id`),

                KEY `idx_product_id` (`product_id`),

                KEY `idx_shipping_method_id` (`shipping_method_id`),

                KEY `idx_status` (`status`),

                KEY `idx_scheduled_ship_date`
                    (`scheduled_ship_date`),

                KEY `idx_shipping_schedule`
                    (`status`, `scheduled_ship_date`),

                KEY `idx_tracking_number`
                    (`tracking_number`),


                /*
                |--------------------------------------------------------------------------
                | Foreign Keys
                |--------------------------------------------------------------------------
                */


                CONSTRAINT `fk_hks_shipments_order`
                    FOREIGN KEY (`order_id`)
                    REFERENCES `hks_orders` (`id`)
                    ON UPDATE CASCADE
                    ON DELETE RESTRICT,

                CONSTRAINT `fk_hks_shipments_subscription`
                    FOREIGN KEY (`subscription_id`)
                    REFERENCES `hks_subscriptions` (`id`)
                    ON UPDATE CASCADE
                    ON DELETE RESTRICT,

                CONSTRAINT `fk_hks_shipments_product`
                    FOREIGN KEY (`product_id`)
                    REFERENCES `hks_products` (`id`)
                    ON UPDATE CASCADE
                    ON DELETE SET NULL,

                CONSTRAINT `fk_hks_shipments_shipping_method`
                    FOREIGN KEY (`shipping_method_id`)
                    REFERENCES `hks_shipping_methods` (`id`)
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
                'hks_shipmentsテーブルの作成に失敗しました。'
            );
        }
    }
}