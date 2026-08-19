<?php

declare(strict_types=1);

namespace HKS\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Product Repository
 *
 * hks_products テーブルへのDBアクセスを担当する。
 *
 * 業務ロジックはここには持たせず、
 * 商品データの取得・登録・更新を担当する。
 */
final class ProductRepository
{
    /**
     * WordPress Database
     */
    private \wpdb $wpdb;

    /**
     * 商品テーブル
     */
    private string $table = 'hks_products';

    public function __construct()
    {
        global $wpdb;

        $this->wpdb = $wpdb;
    }

    /**
     * IDから商品取得
     */
    public function findById(int $id): ?array
    {
        $sql = $this->wpdb->prepare(
            "
            SELECT *
            FROM {$this->table}
            WHERE id = %d
            LIMIT 1
            ",
            $id
        );

        $product = $this->wpdb->get_row(
            $sql,
            ARRAY_A
        );

        return $product ?: null;
    }

    /**
     * 商品コードから商品取得
     */
    public function findByProductCode(
        string $productCode
    ): ?array {

        $sql = $this->wpdb->prepare(
            "
            SELECT *
            FROM {$this->table}
            WHERE product_code = %s
            LIMIT 1
            ",
            $productCode
        );

        $product = $this->wpdb->get_row(
            $sql,
            ARRAY_A
        );

        return $product ?: null;
    }

    /**
     * 商品コード重複確認
     */
    public function existsByProductCode(
        string $productCode
    ): bool {

        $sql = $this->wpdb->prepare(
            "
            SELECT COUNT(*)
            FROM {$this->table}
            WHERE product_code = %s
            ",
            $productCode
        );

        return (int) $this->wpdb->get_var($sql) > 0;
    }

    /**
     * 指定商品を除外して商品コード重複確認
     */
    public function existsByProductCodeExceptId(
        string $productCode,
        int $excludeId
    ): bool {

        $sql = $this->wpdb->prepare(
            "
            SELECT COUNT(*)
            FROM {$this->table}
            WHERE product_code = %s
            AND id <> %d
            ",
            $productCode,
            $excludeId
        );

        return (int) $this->wpdb->get_var($sql) > 0;
    }

    /**
     * 商品登録
     *
     * 登録可能なカラムをRepository側でも限定する。
     *
     * @return int|null 作成された商品ID
     */
    public function create(array $data): ?int
    {
        $allowedFields = [
            'series_id',
            'product_code',
            'product_type',
            'name',
            'description',
            'isbn',
            'volume_number',
            'issue_number',
            'publication_year',
            'publication_month',
            'publication_date',
            'image_path',
            'preview_pdf_path',
            'preview_enabled',            
            'tax_rate',
            'tax_type',
            'status',
        ];

        $insertData = [];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $insertData[$field] = $data[$field];
            }
        }

        if ($insertData === []) {
            return null;
        }

        $insertData['created_at'] = current_time('mysql');
        $insertData['updated_at'] = current_time('mysql');

        $result = $this->wpdb->insert(
            $this->table,
            $insertData
        );

        if ($result === false) {
            return null;
        }

        return (int) $this->wpdb->insert_id;
    }

    /**
     * 商品更新
     */
    public function update(
        int $id,
        array $data
    ): bool {

        $allowedFields = [
            'series_id',
            'product_code',
            'product_type',
            'name',
            'description',
            'isbn',
            'volume_number',
            'issue_number',
            'publication_year',
            'publication_month',
            'publication_date',
            'image_path',
            'preview_pdf_path',
            'preview_enabled',
            'tax_rate',
            'tax_type',
            'status',
        ];

        $updateData = [];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        if ($updateData === []) {
            return false;
        }

        $updateData['updated_at'] = current_time('mysql');

        $result = $this->wpdb->update(
            $this->table,
            $updateData,
            [
                'id' => $id,
            ]
        );

        return $result !== false;
    }

    /**
     * 商品一覧取得
     */
    public function all(): array
    {
        return $this->wpdb->get_results(
            "
            SELECT *
            FROM {$this->table}
            ORDER BY id DESC
            ",
            ARRAY_A
        ) ?: [];
    }

    /**
     * 有効商品一覧取得
     */
    public function active(): array
    {
        $sql = $this->wpdb->prepare(
            "
            SELECT *
            FROM {$this->table}
            WHERE status = %s
            ORDER BY id DESC
            ",
            'active'
        );

        return $this->wpdb->get_results(
            $sql,
            ARRAY_A
        ) ?: [];
    }

    /**
     * 商品種別から取得
     *
     * book / volume / issue / software
     */
    public function findByType(
        string $productType
    ): array {

        $sql = $this->wpdb->prepare(
            "
            SELECT *
            FROM {$this->table}
            WHERE product_type = %s
            ORDER BY id DESC
            ",
            $productType
        );

        return $this->wpdb->get_results(
            $sql,
            ARRAY_A
        ) ?: [];
    }

    /**
     * シリーズに属する商品を取得
     */
    public function findBySeriesId(
        int $seriesId
    ): array {

        $sql = $this->wpdb->prepare(
            "
            SELECT *
            FROM {$this->table}
            WHERE series_id = %d
            ORDER BY publication_date ASC, id ASC
            ",
            $seriesId
        );

        return $this->wpdb->get_results(
            $sql,
            ARRAY_A
        ) ?: [];
    }

    /**
     * 商品を販売終了状態にする。
     *
     * 注文・在庫・ライセンス等との履歴を守るため、
     * 商品マスターは原則として物理削除しない。
     */
    public function discontinue(int $id): bool
    {
        return $this->update(
            $id,
            [
                'status' => 'discontinued',
            ]
        );
    }
}