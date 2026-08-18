<?php

declare(strict_types=1);

namespace HKS\Database\Migrations;


/**
 * hks_inventory テーブル作成Migration
 *
 * 商品単位の現在庫を管理する。
 *
 * 対象:
 * - 通常単行本
 * - 書籍シリーズ各巻
 * - 季刊誌各号
 * - 月刊誌各号
 *
 * software商品は物理在庫を持たないため、
 * 原則として在庫レコードを作成しない。
 *
 * 在庫増減の履歴は
 * hks_inventory_logs で管理する。
 */
final class CreateInventoryTable
{
    /**
     * Migrationを実行する。
     */
    public static function up(): void
    {
        global $wpdb;


        $tableName = 'hks_inventory';


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
                | Product
                |--------------------------------------------------------------------------
                */


                `product_id` bigint(20) unsigned NOT NULL
                    COMMENT '在庫管理対象のhks_products.id',


                /*
                |--------------------------------------------------------------------------
                | Stock
                |--------------------------------------------------------------------------
                */


                `quantity_on_hand` int unsigned NOT NULL DEFAULT 0
                    COMMENT '現在庫数',

                `quantity_reserved` int unsigned NOT NULL DEFAULT 0
                    COMMENT '注文等で引当済みの数量',

                `reorder_level` int unsigned DEFAULT NULL
                    COMMENT '発注・補充を検討する在庫水準',


                /*
                |--------------------------------------------------------------------------
                | Stock Information
                |--------------------------------------------------------------------------
                */


                `last_stocked_at` datetime DEFAULT NULL
                    COMMENT '最終入庫日時',


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

                UNIQUE KEY `uk_product_id` (`product_id`),

                KEY `idx_quantity_on_hand` (`quantity_on_hand`),

                KEY `idx_reorder_level` (`reorder_level`),


                /*
                |--------------------------------------------------------------------------
                | Foreign Keys
                |--------------------------------------------------------------------------
                */


                CONSTRAINT `fk_hks_inventory_product`
                    FOREIGN KEY (`product_id`)
                    REFERENCES `hks_products` (`id`)
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
                'hks_inventoryテーブルの作成に失敗しました。'
            );
        }
    }
}