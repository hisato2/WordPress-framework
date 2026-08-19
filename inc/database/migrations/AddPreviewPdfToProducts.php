<?php

declare(strict_types=1);

namespace HKS\Database\Migrations;

/**
 * hks_products お試しPDFカラム追加Migration
 *
 * 書籍・巻・刊行物の商品に対して、
 * 管理者が作成したお試しPDFを
 * 1商品につき1ファイル登録できるようにする。
 */
final class AddPreviewPdfToProducts
{
    /**
     * Migrationを実行する。
     */
    public static function up(): void
    {
        global $wpdb;

        $tableName = 'hks_products';

        /*
        |--------------------------------------------------------------------------
        | preview_pdf_path
        |--------------------------------------------------------------------------
        */

        $existingColumn = $wpdb->get_var(
            $wpdb->prepare(
                "
                SHOW COLUMNS
                FROM `{$tableName}`
                LIKE %s
                ",
                'preview_pdf_path'
            )
        );

        if ($existingColumn === null) {

            $result = $wpdb->query(
                "
                ALTER TABLE `{$tableName}`
                ADD COLUMN `preview_pdf_path`
                    varchar(500) DEFAULT NULL
                    COMMENT 'お試しPDFの相対パス'
                    AFTER `image_path`
                "
            );

            if ($result === false) {
                throw new \RuntimeException(
                    'preview_pdf_pathカラムの追加に失敗しました。'
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | preview_enabled
        |--------------------------------------------------------------------------
        */

        $existingColumn = $wpdb->get_var(
            $wpdb->prepare(
                "
                SHOW COLUMNS
                FROM `{$tableName}`
                LIKE %s
                ",
                'preview_enabled'
            )
        );

        if ($existingColumn === null) {

            $result = $wpdb->query(
                "
                ALTER TABLE `{$tableName}`
                ADD COLUMN `preview_enabled`
                    tinyint(1) unsigned NOT NULL DEFAULT 0
                    COMMENT 'お試しPDF公開フラグ 1=公開 0=非公開'
                    AFTER `preview_pdf_path`
                "
            );

            if ($result === false) {
                throw new \RuntimeException(
                    'preview_enabledカラムの追加に失敗しました。'
                );
            }
        }
    }
}