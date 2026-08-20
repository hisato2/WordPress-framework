<?php

declare(strict_types=1);

namespace HKS\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Sales Option Repository
 *
 * hks_sales_options テーブルへのDBアクセスを担当する。
 *
 * 商品・シリーズの販売条件、
 * 販売価格、送料設定などを管理する。
 *
 * 業務ロジックはここには持たせず、
 * データの取得・登録・更新を担当する。
 */
final class SalesOptionRepository
{
    /**
     * WordPress Database
     */
    private \wpdb $wpdb;

    /**
     * 販売条件テーブル
     */
    private string $table = 'hks_sales_options';


    public function __construct()
    {
        global $wpdb;

        $this->wpdb = $wpdb;
    }


    /**
     * IDから販売条件を取得
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

        $salesOption = $this->wpdb->get_row(
            $sql,
            ARRAY_A
        );

        return $salesOption ?: null;
    }


    /**
     * 商品IDから販売条件を取得
     *
     * 1商品に複数の販売条件を持たせられる設計のため、
     * 配列で返す。
     */
    public function findByProductId(
        int $productId
    ): array {

        $sql = $this->wpdb->prepare(
            "
            SELECT *
            FROM {$this->table}
            WHERE product_id = %d
            ORDER BY id ASC
            ",
            $productId
        );

        return $this->wpdb->get_results(
            $sql,
            ARRAY_A
        ) ?: [];
    }


    /**
     * シリーズIDから販売条件を取得
     *
     * 主に定期購読用。
     */
    public function findBySeriesId(
        int $seriesId
    ): array {

        $sql = $this->wpdb->prepare(
            "
            SELECT *
            FROM {$this->table}
            WHERE series_id = %d
            ORDER BY id ASC
            ",
            $seriesId
        );

        return $this->wpdb->get_results(
            $sql,
            ARRAY_A
        ) ?: [];
    }


    /**
     * 商品の単品販売条件を取得
     */
    public function findSingleByProductId(
        int $productId
    ): ?array {

        $sql = $this->wpdb->prepare(
            "
            SELECT *
            FROM {$this->table}
            WHERE product_id = %d
            AND sales_type = %s
            LIMIT 1
            ",
            $productId,
            'single'
        );

        $salesOption = $this->wpdb->get_row(
            $sql,
            ARRAY_A
        );

        return $salesOption ?: null;
    }


    /**
     * シリーズの定期購読販売条件を取得
     *
     * 現段階では、
     * 1シリーズにつき subscription の販売条件は
     * 1件として扱う。
     */
    public function findSubscriptionBySeriesId(
        int $seriesId
    ): ?array {

        $sql = $this->wpdb->prepare(
            "
            SELECT *
            FROM {$this->table}
            WHERE series_id = %d
            AND sales_type = %s
            LIMIT 1
            ",
            $seriesId,
            'subscription'
        );

        $salesOption = $this->wpdb->get_row(
            $sql,
            ARRAY_A
        );

        return $salesOption ?: null;
    }





/**
 * 定期購読販売条件の一覧を取得
 *
 * シリーズ情報を結合して、
 * 定期購読管理一覧で使用する。
 *
 * @return array<int, array<string, mixed>>
 */
    public function findAllSubscriptions(): array
    {
        $sql = "
            SELECT
                so.*,
                ps.series_code,
                ps.series_type,
                ps.name AS series_name
            FROM {$this->table} AS so
            INNER JOIN hks_product_series AS ps
                ON ps.id = so.series_id
            WHERE so.sales_type = 'subscription'
            AND ps.series_type IN (
                'quarterly',
                'monthly'
            )
            ORDER BY
                ps.name ASC,
                so.id ASC
        ";

        return $this->wpdb->get_results(
            $sql,
            ARRAY_A
        ) ?: [];
    }

















    /**
     * 販売条件登録
     *
     * @return int|null 作成された販売条件ID
     */
    public function create(array $data): ?int
    {
        $allowedFields = [
            'product_id',
            'series_id',
            'sales_type',
            'name',
            'price',
            'subscription_months',
            'scheduled_shipments',
            'shipping_policy',
            'shipping_amount',
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
     * 販売条件更新
     */
    public function update(
        int $id,
        array $data
    ): bool {

        $allowedFields = [
            'product_id',
            'series_id',
            'sales_type',
            'name',
            'price',
            'subscription_months',
            'scheduled_shipments',
            'shipping_policy',
            'shipping_amount',
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
     * 販売条件を無効化
     *
     * 注文履歴との整合性を維持するため、
     * 原則として物理削除しない。
     */
    public function deactivate(
        int $id
    ): bool {

        return $this->update(
            $id,
            [
                'status' => 'inactive',
            ]
        );
    }
}