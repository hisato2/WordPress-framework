<?php

declare(strict_types=1);

namespace HKS\Database\Migrations;


/**
 * hks_shipping_methods テーブル作成Migration
 *
 * 配送方法および基本配送料を管理する。
 *
 * 例:
 * - ゆうメール
 * - クリックポスト
 * - 宅配便
 * - 送料無料
 *
 * 実際の注文時・発送時の送料は、
 * 注文・発送データ側にもスナップショットとして保存する。
 */
final class CreateShippingMethodsTable
{
    /**
     * Migrationを実行する。
     */
    public static function up(): void
    {
        global $wpdb;


        $tableName = 'hks_shipping_methods';


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

                `shipping_code` varchar(50) NOT NULL
                    COMMENT '配送方法内部コード',

                `name` varchar(100) NOT NULL
                    COMMENT '配送方法名',

                `description` text DEFAULT NULL
                    COMMENT '配送方法説明',

                `base_fee` int unsigned NOT NULL DEFAULT 0
                    COMMENT '基本配送料',

                `tax_rate` decimal(5,2) NOT NULL DEFAULT 10.00
                    COMMENT '配送料に対する消費税率',

                `tax_type` enum(
                    'included',
                    'excluded',
                    'exempt'
                ) NOT NULL DEFAULT 'included'
                    COMMENT '税込・税抜・非課税',

                `tracking_available` tinyint(1) NOT NULL DEFAULT 0
                    COMMENT '追跡番号の利用可否',

                `sort_order` int unsigned NOT NULL DEFAULT 0
                    COMMENT '表示順',

                `status` enum(
                    'active',
                    'inactive'
                ) NOT NULL DEFAULT 'active'
                    COMMENT '利用状態',

                `created_at` datetime DEFAULT CURRENT_TIMESTAMP,

                `updated_at` datetime DEFAULT CURRENT_TIMESTAMP
                    ON UPDATE CURRENT_TIMESTAMP,

                PRIMARY KEY (`id`),

                UNIQUE KEY `uk_shipping_code` (`shipping_code`),

                KEY `idx_status` (`status`),

                KEY `idx_sort_order` (`sort_order`)

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
                'hks_shipping_methodsテーブルの作成に失敗しました。'
            );
        }
    }
}