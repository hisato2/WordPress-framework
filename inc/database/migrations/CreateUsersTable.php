<?php

declare(strict_types=1);


namespace HKS\Database\Migrations;


/**
 * hks_users テーブル作成Migration
 */
final class CreateUsersTable
{
    /**
     * Migrationを実行する。
     */
    public static function up(): void
    {
        global $wpdb;


        $tableName = 'hks_users';


        /*
        |--------------------------------------------------------------------------
        | 既存テーブル確認
        |--------------------------------------------------------------------------
        |
        | 既にhks_usersが存在する場合は何もしない。
        | 現在の会員データを破壊しないため。
        |
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
                `email` varchar(255) NOT NULL,
                `password_hash` varchar(255) DEFAULT NULL,
                `line_user_id` varchar(100) DEFAULT NULL,
                `line_display_name` varchar(100) DEFAULT NULL,
                `line_picture_url` varchar(255) DEFAULT NULL,
                `last_name` varchar(50) NOT NULL,
                `first_name` varchar(50) NOT NULL,
                `last_name_kana` varchar(50) DEFAULT NULL,
                `first_name_kana` varchar(50) DEFAULT NULL,
                `phone` varchar(20) DEFAULT NULL,
                `postal_code` varchar(10) DEFAULT NULL,
                `prefecture` varchar(50) DEFAULT NULL,
                `city` varchar(100) DEFAULT NULL,
                `address1` varchar(255) DEFAULT NULL,
                `address2` varchar(255) DEFAULT NULL,
                `birthday` date DEFAULT NULL,
                `profile_image` varchar(255) DEFAULT NULL,
                `role` enum(
                    'super_admin',
                    'admin',
                    'manager',
                    'staff',
                    'member'
                ) DEFAULT 'member',
                `status` enum(
                    'temporary',
                    'active',
                    'suspended',
                    'deleted'
                ) DEFAULT 'temporary',
                `email_verified_at` datetime DEFAULT NULL
                    COMMENT 'メール認証完了日時',
                `remember_token` varchar(100) DEFAULT NULL
                    COMMENT 'ログイン状態の保持（Remember Me）',
                `verify_token` varchar(100) DEFAULT NULL
                    COMMENT 'メール認証用トークン',
                `verify_expires_at` datetime DEFAULT NULL
                    COMMENT 'メール認証トークンの有効期限',
                `reset_token` varchar(100) DEFAULT NULL
                    COMMENT 'パスワードリセット用トークン',
                `reset_expires_at` datetime DEFAULT NULL
                    COMMENT 'パスワードリセットの有効期限',
                `line_linked_at` datetime DEFAULT NULL
                    COMMENT 'LINE連携日時',
                `last_login_at` datetime DEFAULT NULL
                    COMMENT '最終ログイン日時',
                `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                `updated_at` datetime DEFAULT CURRENT_TIMESTAMP
                    ON UPDATE CURRENT_TIMESTAMP,
                PRIMARY KEY (`id`),
                UNIQUE KEY `uk_email` (`email`),
                UNIQUE KEY `uk_line` (`line_user_id`),
                KEY `idx_status` (`status`),
                KEY `idx_role` (`role`)
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
                'hks_usersテーブルの作成に失敗しました。'
            );
        }
    }
}