<?php

declare(strict_types=1);

namespace HKS\Database\Migrations;

/**
 * hks_products.product_type に
 * quarterly / monthly を追加するMigration
 *
 * 既存のhks_productsテーブルを維持したまま、
 * 季刊誌・月刊誌を登録可能にする。
 */
final class AddPublicationProductTypes
{
    /**
     * Migrationを実行する。
     */
    public static function up(): void
    {
        global $wpdb;

        $tableName = 'hks_products';

        /*
        |----------------------------------------------------------------------
        | Table確認
        |----------------------------------------------------------------------
        |
        | hks_products がまだ存在しない場合は何もしない。
        |
        */

        $existingTable = $wpdb->get_var(
            $wpdb->prepare(
                'SHOW TABLES LIKE %s',
                $tableName
            )
        );

        if ($existingTable !== $tableName) {
            return;
        }

        /*
        |----------------------------------------------------------------------
        | Column確認
        |----------------------------------------------------------------------
        */

        $column = $wpdb->get_row(
            "
            SHOW COLUMNS
            FROM `{$tableName}`
            LIKE 'product_type'
            ",
            ARRAY_A
        );

        if (!$column) {
            throw new \RuntimeException(
                'hks_products.product_type が見つかりません。'
            );
        }

        /*
        |----------------------------------------------------------------------
        | Migration済み確認
        |----------------------------------------------------------------------
        |
        | quarterly / monthly の両方が既に存在する場合は
        | ALTER TABLEを実行しない。
        |
        */

        $type = (string) ($column['Type'] ?? '');

        $hasQuarterly = preg_match(
            "/'quarterly'/",
            $type
        ) === 1;

        $hasMonthly = preg_match(
            "/'monthly'/",
            $type
        ) === 1;

        if ($hasQuarterly && $hasMonthly) {
            return;
        }

        /*
        |----------------------------------------------------------------------
        | Add Publication Product Types
        |----------------------------------------------------------------------
        |
        | issue は旧商品種別だが、このMigrationでは削除しない。
        | 完全撤去は既存データとの整合性確認後に別Migrationで行う。
        |
        */

        $sql = "
            ALTER TABLE `{$tableName}`
            MODIFY COLUMN `product_type`
            ENUM(
                'book',
                'volume',
                'issue',
                'quarterly',
                'monthly',
                'software'
            )
            NOT NULL
            COMMENT '商品種別'
        ";

        $result = $wpdb->query($sql);

        /*
        |----------------------------------------------------------------------
        | Error Handling
        |----------------------------------------------------------------------
        */

        if ($result === false) {
            throw new \RuntimeException(
                'hks_products.product_type への '
                . 'quarterly / monthly 追加に失敗しました: '
                . $wpdb->last_error
            );
        }
    }
}