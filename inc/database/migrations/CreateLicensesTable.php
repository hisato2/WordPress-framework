<?php

declare(strict_types=1);

namespace HKS\Database\Migrations;


/**
 * hks_licenses テーブル作成Migration
 *
 * Windowsソフトウェア商品のライセンスを管理する。
 *
 * ソフトウェア商品の購入・決済完了後に
 * ライセンスを発行する。
 *
 * ライセンスキーそのものは平文保存せず、
 * 検証用ハッシュのみを保存する。
 *
 * 初回アクティベーション時にHardware IDを登録し、
 * 以降は登録済みHardware IDとの一致を確認する。
 */
final class CreateLicensesTable
{
    /**
     * Migrationを実行する。
     */
    public static function up(): void
    {
        global $wpdb;


        $tableName = 'hks_licenses';


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
                    COMMENT 'ライセンス所有者 hks_users.id',

                `product_id` bigint(20) unsigned NOT NULL
                    COMMENT 'software商品 hks_products.id',

                `order_item_id` bigint(20) unsigned NOT NULL
                    COMMENT '購入元 hks_order_items.id',


                /*
                |--------------------------------------------------------------------------
                | License Key
                |--------------------------------------------------------------------------
                |
                | ライセンスキーの平文は保存しない。
                |
                | license_key_hash:
                | 検証用ハッシュ
                |
                | license_key_last4:
                | 管理画面でライセンスを識別するための
                | キー末尾4文字
                |
                */


                `license_key_hash` varchar(255) NOT NULL
                    COMMENT 'ライセンスキー検証用ハッシュ',

                `license_key_last4` char(4) NOT NULL
                    COMMENT 'ライセンスキー末尾4文字',


                /*
                |--------------------------------------------------------------------------
                | Hardware Authentication
                |--------------------------------------------------------------------------
                */


                `hardware_id` varchar(255) DEFAULT NULL
                    COMMENT '認証済みHardware ID',


                /*
                |--------------------------------------------------------------------------
                | License Status
                |--------------------------------------------------------------------------
                */


                `status` enum(
                    'issued',
                    'active',
                    'suspended',
                    'revoked',
                    'expired'
                ) NOT NULL DEFAULT 'issued'
                    COMMENT 'ライセンス状態',


                /*
                |--------------------------------------------------------------------------
                | License Dates
                |--------------------------------------------------------------------------
                */


                `issued_at` datetime DEFAULT CURRENT_TIMESTAMP
                    COMMENT 'ライセンス発行日時',

                `activated_at` datetime DEFAULT NULL
                    COMMENT '初回アクティベーション日時',

                `expires_at` datetime DEFAULT NULL
                    COMMENT 'ライセンス有効期限。無期限の場合NULL',


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

                UNIQUE KEY `uk_license_key_hash`
                    (`license_key_hash`),

                UNIQUE KEY `uk_order_item_id`
                    (`order_item_id`),

                KEY `idx_user_id`
                    (`user_id`),

                KEY `idx_product_id`
                    (`product_id`),

                KEY `idx_hardware_id`
                    (`hardware_id`),

                KEY `idx_status`
                    (`status`),

                KEY `idx_expires_at`
                    (`expires_at`),

                KEY `idx_user_product`
                    (`user_id`, `product_id`),


                /*
                |--------------------------------------------------------------------------
                | Foreign Keys
                |--------------------------------------------------------------------------
                */


                CONSTRAINT `fk_hks_licenses_user`
                    FOREIGN KEY (`user_id`)
                    REFERENCES `hks_users` (`id`)
                    ON UPDATE CASCADE
                    ON DELETE RESTRICT,

                CONSTRAINT `fk_hks_licenses_product`
                    FOREIGN KEY (`product_id`)
                    REFERENCES `hks_products` (`id`)
                    ON UPDATE CASCADE
                    ON DELETE RESTRICT,

                CONSTRAINT `fk_hks_licenses_order_item`
                    FOREIGN KEY (`order_item_id`)
                    REFERENCES `hks_order_items` (`id`)
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
                'hks_licensesテーブルの作成に失敗しました。'
            );
        }
    }
}