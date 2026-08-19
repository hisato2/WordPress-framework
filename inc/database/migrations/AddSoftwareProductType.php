<?php

declare(strict_types=1);

namespace HKS\Database\Migrations;

/**
 * hks_products.product_type に
 * software を追加するMigration
 *
 * 既存のhks_productsテーブルを維持したまま、
 * ソフトウェア商品を登録可能にする。
 */
final class AddSoftwareProductType
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
        | 新規環境では CreateProductsTable が
        | software を含む定義でテーブルを作成する。
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
        | Typeの中に software が既に存在する場合は
        | ALTER TABLEを実行しない。
        |
        */

        $type = (string) ($column['Type'] ?? '');

        if (
            preg_match(
                "/'software'/",
                $type
            ) === 1
        ) {
            return;
        }

        /*
        |----------------------------------------------------------------------
        | Add Software Product Type
        |----------------------------------------------------------------------
        */

        $sql = "
            ALTER TABLE `{$tableName}`
            MODIFY COLUMN `product_type`
            ENUM(
                'book',
                'volume',
                'issue',
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
                . 'software 追加に失敗しました: '
                . $wpdb->last_error
            );
        }
    }
}