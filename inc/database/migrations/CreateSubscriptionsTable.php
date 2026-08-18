<?php

declare(strict_types=1);

namespace HKS\Database\Migrations;


/**
 * hks_subscriptions テーブル作成Migration
 *
 * 定期購読契約を管理する。
 *
 * 例:
 * - 季刊誌 12か月 / 4回発送
 * - 月刊会報 12か月 / 12回発送
 *
 * 購読対象そのものは hks_product_series を参照し、
 * 購読を発生させた注文・注文明細も保持する。
 *
 * 実際の発送履歴は hks_shipments で管理する。
 */
final class CreateSubscriptionsTable
{
    /**
     * Migrationを実行する。
     */
    public static function up(): void
    {
        global $wpdb;


        $tableName = 'hks_subscriptions';


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

                `user_id` bigint(20) unsigned NOT NULL
                    COMMENT '購読者 hks_users.id',

                `order_id` bigint(20) unsigned NOT NULL
                    COMMENT '購読を発生させた注文 hks_orders.id',

                `order_item_id` bigint(20) unsigned NOT NULL
                    COMMENT '購読商品の注文明細 hks_order_items.id',

                `series_id` bigint(20) unsigned NOT NULL
                    COMMENT '購読対象シリーズ hks_product_series.id',

                /*
                |--------------------------------------------------------------------------
                | Subscription Terms
                |--------------------------------------------------------------------------
                */

                `subscription_months` smallint unsigned NOT NULL
                    COMMENT '購読期間（月数）',

                `scheduled_shipments` smallint unsigned NOT NULL
                    COMMENT '契約期間中の予定発送回数',

                `start_date` date NOT NULL
                    COMMENT '購読開始日',

                `end_date` date NOT NULL
                    COMMENT '購読終了日',

                `next_shipment_date` date DEFAULT NULL
                    COMMENT '次回発送予定日',

                /*
                |--------------------------------------------------------------------------
                | Status
                |--------------------------------------------------------------------------
                */

                `status` enum(
                    'active',
                    'completed',
                    'cancelled',
                    'suspended'
                ) NOT NULL DEFAULT 'active'
                    COMMENT '購読契約状態',

                `cancelled_at` datetime DEFAULT NULL
                    COMMENT '解約日時',

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

                UNIQUE KEY `uk_order_item_id` (`order_item_id`),

                KEY `idx_user_id` (`user_id`),

                KEY `idx_order_id` (`order_id`),

                KEY `idx_series_id` (`series_id`),

                KEY `idx_status` (`status`),

                KEY `idx_next_shipment`
                    (`status`, `next_shipment_date`),

                CONSTRAINT `fk_hks_subscriptions_user`
                    FOREIGN KEY (`user_id`)
                    REFERENCES `hks_users` (`id`)
                    ON UPDATE CASCADE
                    ON DELETE RESTRICT,

                CONSTRAINT `fk_hks_subscriptions_order`
                    FOREIGN KEY (`order_id`)
                    REFERENCES `hks_orders` (`id`)
                    ON UPDATE CASCADE
                    ON DELETE RESTRICT,

                CONSTRAINT `fk_hks_subscriptions_order_item`
                    FOREIGN KEY (`order_item_id`)
                    REFERENCES `hks_order_items` (`id`)
                    ON UPDATE CASCADE
                    ON DELETE RESTRICT,

                CONSTRAINT `fk_hks_subscriptions_series`
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
                'hks_subscriptionsテーブルの作成に失敗しました。'
            );
        }
    }
}