<?php

declare(strict_types=1);

namespace HKS\Products;

use HKS\Repositories\ProductRepository;
use HKS\Repositories\ProductSeriesRepository;

/**
 * Product Service
 *
 * 商品に関する業務ロジックを担当する。
 *
 * Repository:
 * DBへの読み書き
 *
 * Service:
 * 入力値検証・業務ルール・Repository連携
 */
final class ProductService
{
    private ProductRepository $products;

    private ProductSeriesRepository $series;

    /**
     * 商品種別
     */
    private const PRODUCT_TYPES = [
        'book',
        'volume',
        'issue',
        'software',
    ];

    /**
     * 商品状態
     */
    private const PRODUCT_STATUSES = [
        'draft',
        'active',
        'inactive',
        'discontinued',
    ];

    public function __construct(
        ?ProductRepository $products = null,
        ?ProductSeriesRepository $series = null
    ) {
        $this->products = $products ?? new ProductRepository();
        $this->series = $series ?? new ProductSeriesRepository();
    }

    /**
     * 商品登録
     *
     * @return int 作成された商品ID
     */
    public function create(array $data): int
    {
        $data = $this->normalize($data);

        $this->validate($data);

        /*
        |----------------------------------------------------------------------
        | Product Code
        |----------------------------------------------------------------------
        */

        if (
            $this->products->existsByProductCode(
                $data['product_code']
            )
        ) {
            throw new \InvalidArgumentException(
                'この商品コードは既に使用されています。'
            );
        }

        /*
        |----------------------------------------------------------------------
        | Series
        |----------------------------------------------------------------------
        */

        $this->validateSeries(
            $data['series_id']
        );

        /*
        |----------------------------------------------------------------------
        | Create
        |----------------------------------------------------------------------
        */

        $productId = $this->products->create($data);

        if ($productId === null) {
            throw new \RuntimeException(
                '商品の登録に失敗しました。'
            );
        }

        return $productId;
    }

    /**
     * 商品更新
     */
    public function update(
        int $productId,
        array $data
    ): bool {

        $product = $this->products->findById($productId);

        if ($product === null) {
            throw new \InvalidArgumentException(
                '指定された商品が存在しません。'
            );
        }

        /*
         * 部分更新にも対応するため、
         * 現在値と更新値を結合して検証する。
         */
        $merged = array_merge(
            $product,
            $data
        );

        $merged = $this->normalize($merged);

        $this->validate($merged);

        /*
        |----------------------------------------------------------------------
        | Product Code
        |----------------------------------------------------------------------
        */

        if (
            $this->products->existsByProductCodeExceptId(
                $merged['product_code'],
                $productId
            )
        ) {
            throw new \InvalidArgumentException(
                'この商品コードは既に使用されています。'
            );
        }

        /*
        |----------------------------------------------------------------------
        | Series
        |----------------------------------------------------------------------
        */

        $this->validateSeries(
            $merged['series_id']
        );

        return $this->products->update(
            $productId,
            $merged
        );
    }

    /**
     * 商品取得
     */
    public function find(int $productId): ?array
    {
        return $this->products->findById(
            $productId
        );
    }

    /**
     * 商品一覧
     */
    public function all(): array
    {
        return $this->products->all();
    }

    /**
     * 有効商品一覧
     */
    public function active(): array
    {
        return $this->products->active();
    }

    /**
     * 商品販売終了
     */
    public function discontinue(int $productId): bool
    {
        if ($this->products->findById($productId) === null) {
            throw new \InvalidArgumentException(
                '指定された商品が存在しません。'
            );
        }

        return $this->products->discontinue(
            $productId
        );
    }

    /**
     * 入力値正規化
     */
    private function normalize(array $data): array
    {
        /*
         * 文字列
         */
        foreach (
            [
                'product_code',
                'product_type',
                'name',
                'description',
                'isbn',
                'volume_number',
                'issue_number',
                'image_path',
                'preview_pdf_path',                
                'tax_type',
                'status',
            ] as $field
        ) {
            if (
                isset($data[$field]) &&
                is_string($data[$field])
            ) {
                $data[$field] = trim(
                    $data[$field]
                );
            }
        }

        /*
         * 空文字はNULLとして扱う項目
         */
        foreach (
            [
                'description',
                'isbn',
                'volume_number',
                'issue_number',
                'publication_date',
                'image_path',
                'preview_pdf_path',                
            ] as $field
        ) {
            if (
                array_key_exists($field, $data) &&
                $data[$field] === ''
            ) {
                $data[$field] = null;
            }
        }

        /*
         * Series ID
         */
        if (
            !isset($data['series_id']) ||
            $data['series_id'] === '' ||
            $data['series_id'] === 0 ||
            $data['series_id'] === '0'
        ) {
            $data['series_id'] = null;
        } else {
            $data['series_id'] = (int) $data['series_id'];
        }

        /*
         * Publication Year
         */
        if (
            !isset($data['publication_year']) ||
            $data['publication_year'] === ''
        ) {
            $data['publication_year'] = null;
        } else {
            $data['publication_year']
                = (int) $data['publication_year'];
        }

        /*
         * Publication Month
         */
        if (
            !isset($data['publication_month']) ||
            $data['publication_month'] === ''
        ) {
            $data['publication_month'] = null;
        } else {
            $data['publication_month']
                = (int) $data['publication_month'];
        }

        /*
        * Preview Enabled
        */
        if (
            !isset($data['preview_enabled']) ||
            (int) $data['preview_enabled'] !== 1
        ) {
            $data['preview_enabled'] = 0;
        } else {
            $data['preview_enabled'] = 1;
        }

        /*
         * Tax Rate
         */
        if (
            !isset($data['tax_rate']) ||
            $data['tax_rate'] === ''
        ) {
            $data['tax_rate'] = 10.00;
        }

        /*
         * Default Values
         */
        $data['tax_type']
            = $data['tax_type'] ?? 'included';

        $data['status']
            = $data['status'] ?? 'draft';

        return $data;
    }

    /**
     * 商品データ検証
     */
    private function validate(array $data): void
    {
        /*
        |----------------------------------------------------------------------
        | Required
        |----------------------------------------------------------------------
        */

        if (
            empty($data['product_code']) ||
            !is_string($data['product_code'])
        ) {
            throw new \InvalidArgumentException(
                '商品コードを入力してください。'
            );
        }

        if (
            empty($data['name']) ||
            !is_string($data['name'])
        ) {
            throw new \InvalidArgumentException(
                '商品名を入力してください。'
            );
        }

        if (
            empty($data['product_type']) ||
            !in_array(
                $data['product_type'],
                self::PRODUCT_TYPES,
                true
            )
        ) {
            throw new \InvalidArgumentException(
                '商品種別が正しくありません。'
            );
        }

        /*
        |----------------------------------------------------------------------
        | Status
        |----------------------------------------------------------------------
        */

        if (
            !in_array(
                $data['status'],
                self::PRODUCT_STATUSES,
                true
            )
        ) {
            throw new \InvalidArgumentException(
                '商品状態が正しくありません。'
            );
        }

        /*
        |----------------------------------------------------------------------
        | Publication Month
        |----------------------------------------------------------------------
        */

        if (
            $data['publication_month'] !== null &&
            (
                $data['publication_month'] < 1 ||
                $data['publication_month'] > 12
            )
        ) {
            throw new \InvalidArgumentException(
                '発行月は1〜12で指定してください。'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Product Type Rules
        |--------------------------------------------------------------------------
        |
        | シリーズ巻（volume）のみシリーズ指定を必須とする。
        |
        | book:
        |   通常の単行本。シリーズなしで登録可能。
        |
        | volume:
        |   シリーズに属する巻。series_id 必須。
        |
        | issue:
        |   雑誌・刊行物。シリーズなしで登録可能。
        |   通し号番号は issue_number で管理する。
        |
        | software:
        |   ソフトウェア。シリーズなしで登録可能。
        |
        */

        if (
            $data['product_type'] === 'volume' &&
            $data['series_id'] === null
        ) {
            throw new \InvalidArgumentException(
                'シリーズ巻にはシリーズの指定が必要です。'
            );
        }

        } // ← validate() を閉じる

        /**
         * シリーズ存在確認
         */
        private function validateSeries(
            ?int $seriesId
        ): void {

            if ($seriesId === null) {
                return;
            }

            if (
                $this->series->findById(
                    $seriesId
                ) === null
            ) {
                throw new \InvalidArgumentException(
                    '指定された商品シリーズが存在しません。'
                );
            }
        }
    }