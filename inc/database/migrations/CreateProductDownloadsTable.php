<?php

declare(strict_types=1);

namespace HKS\Database\Migrations;


/**
 * hks_product_downloads テーブル作成Migration
 *
 * ソフトウェア商品の配布ファイルを管理する。
 *
 * 対象:
 * - VB.NET Windowsデスクトップアプリ
 * - ZIP形式の配布ファイル
 *
 * ファイル本体はOneDrive等の外部ストレージで管理し、
 * このテーブルにはファイルそのものを保存しない。
 *
 * ダウンロード権限は購入履歴ではなく、
 * hks_usersにログイン済みかどうかで判定する。
 */
final class CreateProductDownloadsTable
{
    /**
     * Migrationを実行する。
     */
    public static function up(): void
    {
        global $wpdb;


        $tableName = 'hks_product_downloads';


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
                    COMMENT 'software商品 hks_products.id',


                /*
                |--------------------------------------------------------------------------
                | Version
                |--------------------------------------------------------------------------
                */


                `version` varchar(50) NOT NULL
                    COMMENT 'ソフトウェアバージョン 例: 1.0.0',

                `file_name` varchar(255) NOT NULL
                    COMMENT '配布ZIPファイル名',


                /*
                |--------------------------------------------------------------------------
                | Storage
                |--------------------------------------------------------------------------
                |
                | ファイル本体はDBに保存せず、
                | OneDrive等の外部ストレージで管理する。
                |
                */


                `storage_provider` enum(
                    'onedrive'
                ) NOT NULL DEFAULT 'onedrive'
                    COMMENT 'ファイル保存先',

                `storage_file_id` varchar(500) NOT NULL
                    COMMENT '外部ストレージ上のファイル識別子',


                /*
                |--------------------------------------------------------------------------
                | Publication
                |--------------------------------------------------------------------------
                */


                `published_at` datetime DEFAULT NULL
                    COMMENT '公開日時',

                `is_active` tinyint(1) unsigned NOT NULL DEFAULT 1
                    COMMENT '配布有効フラグ 1=有効 0=無効',


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

                UNIQUE KEY `uk_product_version`
                    (`product_id`, `version`),

                KEY `idx_product_id`
                    (`product_id`),

                KEY `idx_is_active`
                    (`is_active`),

                KEY `idx_published_at`
                    (`published_at`),

                KEY `idx_product_active`
                    (`product_id`, `is_active`),


                /*
                |--------------------------------------------------------------------------
                | Foreign Keys
                |--------------------------------------------------------------------------
                */


                CONSTRAINT `fk_hks_product_downloads_product`
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
                'hks_product_downloadsテーブルの作成に失敗しました。'
            );
        }
    }
}