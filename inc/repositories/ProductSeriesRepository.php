<?php

declare(strict_types=1);

namespace HKS\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Product Series Repository
 *
 * hks_product_series テーブルへの
 * DBアクセスを担当する。
 *
 * 対象:
 * - 書籍シリーズ
 * - 季刊誌
 * - 月刊誌
 */
final class ProductSeriesRepository
{
    /**
     * WordPress Database
     */
    private \wpdb $wpdb;

    /**
     * 商品シリーズテーブル
     */
    private string $table = 'hks_product_series';

    public function __construct()
    {
        global $wpdb;

        $this->wpdb = $wpdb;
    }

    /**
     * IDからシリーズ取得
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

        $series = $this->wpdb->get_row(
            $sql,
            ARRAY_A
        );

        return $series ?: null;
    }

    /**
     * シリーズコードから取得
     */
    public function findBySeriesCode(
        string $seriesCode
    ): ?array {

        $sql = $this->wpdb->prepare(
            "
            SELECT *
            FROM {$this->table}
            WHERE series_code = %s
            LIMIT 1
            ",
            $seriesCode
        );

        $series = $this->wpdb->get_row(
            $sql,
            ARRAY_A
        );

        return $series ?: null;
    }

    /**
     * シリーズコード重複確認
     */
    public function existsBySeriesCode(
        string $seriesCode
    ): bool {

        $sql = $this->wpdb->prepare(
            "
            SELECT COUNT(*)
            FROM {$this->table}
            WHERE series_code = %s
            ",
            $seriesCode
        );

        return (int) $this->wpdb->get_var($sql) > 0;
    }

    /**
     * 指定シリーズを除外して
     * シリーズコード重複確認
     */
    public function existsBySeriesCodeExceptId(
        string $seriesCode,
        int $excludeId
    ): bool {

        $sql = $this->wpdb->prepare(
            "
            SELECT COUNT(*)
            FROM {$this->table}
            WHERE series_code = %s
            AND id <> %d
            ",
            $seriesCode,
            $excludeId
        );

        return (int) $this->wpdb->get_var($sql) > 0;
    }

    /**
     * シリーズ登録
     *
     * @return int|null 作成されたシリーズID
     */
    public function create(array $data): ?int
    {
        $allowedFields = [
            'series_code',
            'series_type',
            'name',
            'description',
            'issn',
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
     * シリーズ更新
     */
    public function update(
        int $id,
        array $data
    ): bool {

        $allowedFields = [
            'series_code',
            'series_type',
            'name',
            'description',
            'issn',
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
     * シリーズ一覧取得
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
     * 有効シリーズ一覧取得
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
     * シリーズ種別から取得
     *
     * book_series / quarterly / monthly
     */
    public function findByType(
        string $seriesType
    ): array {

        $sql = $this->wpdb->prepare(
            "
            SELECT *
            FROM {$this->table}
            WHERE series_type = %s
            ORDER BY id DESC
            ",
            $seriesType
        );

        return $this->wpdb->get_results(
            $sql,
            ARRAY_A
        ) ?: [];
    }

    /**
     * シリーズを販売・刊行終了状態にする。
     *
     * 関連商品や購読履歴を保護するため
     * 原則として物理削除しない。
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