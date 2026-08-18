<?php

declare(strict_types=1);

namespace HKS\Database\Migrations;


/**
 * hks_inventory_logs テーブル作成Migration
 *
 * 在庫の増減・引当・発送・返品・調整等の
 * 履歴を管理する。
 *
 * hks_inventory が現在庫を保持し、
 * hks_inventory_logs がその変更履歴を保持する。
 *
 * 対応例:
 *
 * - 商品入庫
 * - 通常出庫
 * - 注文による在庫引当
 * - 注文キャンセルによる引当解除
 * - 商品発送
 * - 返品
 * - 棚卸・手動調整
 */
final class CreateInventoryLogsTable
{
    /**
     * Migrationを実行する。
     */
    public static function up(): void
    {
        global $wpdb;


        $tableName = 'hks_inventory_logs';


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


                `inventory_id` bigint(20) unsigned NOT NULL
                    COMMENT 'hks_inventory.id',

                `product_id` bigint(20) unsigned NOT NULL
                    COMMENT '在庫変動対象のhks_products.id',

                `order_id` bigint(20) unsigned DEFAULT NULL
                    COMMENT '注文に関連する場合のhks_orders.id',

                `shipment_id` bigint(20) unsigned DEFAULT NULL
                    COMMENT '発送に関連する場合のhks_shipments.id',


                /*
                |--------------------------------------------------------------------------
                | Movement
                |--------------------------------------------------------------------------
                */


                `movement_type` enum(
                    'stock_in',
                    'stock_out',
                    'reserve',
                    'release',
                    'shipment',
                    'return',
                    'adjustment'
                ) NOT NULL
                    COMMENT '在庫変動種別',

                `quantity_change` int NOT NULL
                    COMMENT '在庫変動量。増加は正、減少は負',

                `quantity_before` int unsigned NOT NULL
                    COMMENT '変更前の現在庫数',

                `quantity_after` int unsigned NOT NULL
                    COMMENT '変更後の現在庫数',

                `reserved_before` int unsigned NOT NULL DEFAULT 0
                    COMMENT '変更前の引当数量',

                `reserved_after` int unsigned NOT NULL DEFAULT 0
                    COMMENT '変更後の引当数量',


                /*
                |--------------------------------------------------------------------------
                | Reference / Notes
                |--------------------------------------------------------------------------
                */


                `reference` varchar(255) DEFAULT NULL
                    COMMENT '入庫番号・外部参照番号等',

                `note` text DEFAULT NULL
                    COMMENT '在庫変更理由・管理者メモ',


                /*
                |--------------------------------------------------------------------------
                | Timestamps
                |--------------------------------------------------------------------------
                */


                `created_at` datetime DEFAULT CURRENT_TIMESTAMP,


                /*
                |--------------------------------------------------------------------------
                | Keys
                |--------------------------------------------------------------------------
                */


                PRIMARY KEY (`id`),

                KEY `idx_inventory_id` (`inventory_id`),

                KEY `idx_product_id` (`product_id`),

                KEY `idx_order_id` (`order_id`),

                KEY `idx_shipment_id` (`shipment_id`),

                KEY `idx_movement_type` (`movement_type`),

                KEY `idx_created_at` (`created_at`),

                KEY `idx_product_created`
                    (`product_id`, `created_at`),


                /*
                |--------------------------------------------------------------------------
                | Foreign Keys
                |--------------------------------------------------------------------------
                */


                CONSTRAINT `fk_hks_inventory_logs_inventory`
                    FOREIGN KEY (`inventory_id`)
                    REFERENCES `hks_inventory` (`id`)
                    ON UPDATE CASCADE
                    ON DELETE RESTRICT,

                CONSTRAINT `fk_hks_inventory_logs_product`
                    FOREIGN KEY (`product_id`)
                    REFERENCES `hks_products` (`id`)
                    ON UPDATE CASCADE
                    ON DELETE RESTRICT,

                CONSTRAINT `fk_hks_inventory_logs_order`
                    FOREIGN KEY (`order_id`)
                    REFERENCES `hks_orders` (`id`)
                    ON UPDATE CASCADE
                    ON DELETE SET NULL,

                CONSTRAINT `fk_hks_inventory_logs_shipment`
                    FOREIGN KEY (`shipment_id`)
                    REFERENCES `hks_shipments` (`id`)
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
                'hks_inventory_logsテーブルの作成に失敗しました。'
            );
        }
    }
}