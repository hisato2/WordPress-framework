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
        'quarterly',
        'monthly',
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
            $data['series_id'],
            $data['product_type']
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
            $merged['series_id'],
            $merged['product_type']
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
                'software_version',                
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
                'software_version',                
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
| 商品種別ごとのシリーズ指定ルール。
|
| book:
|   単行本。シリーズ指定なし。
|
| volume:
|   シリーズ本。書籍シリーズの指定必須。
|
| quarterly:
|   季刊誌。季刊誌マスターの指定必須。
|
| monthly:
|   月刊誌。月刊誌マスターの指定必須。
|
| software:
|   ソフトウェア。シリーズ指定なし。
|
*/

            if (
                in_array(
                    $data['product_type'],
                    [
                        'volume',
                        'quarterly',
                        'monthly',
                    ],
                    true
                )
                && $data['series_id'] === null
            ) {
                throw new \InvalidArgumentException(
                    'この商品種別には所属シリーズの指定が必要です。'
                );
            }

            if (
                in_array(
                    $data['product_type'],
                    [
                        'book',
                        'software',
                    ],
                    true
                )
                && $data['series_id'] !== null
            ) {
                throw new \InvalidArgumentException(
                    'この商品種別には所属シリーズを指定できません。'
                );
            }

        } // ← validate() を閉じる
/**
 * 商品種別とシリーズ種別の整合性確認
 */
        private function validateSeries(
            ?int $seriesId,
            string $productType
        ): void {

            /*
            |--------------------------------------------------------------------------
            | Seriesなし
            |--------------------------------------------------------------------------
            */

            if ($seriesId === null) {
                return;
            }


            /*
            |--------------------------------------------------------------------------
            | Series取得
            |--------------------------------------------------------------------------
            */

            $series = $this->series->findById(
                $seriesId
            );

            if ($series === null) {
                throw new \InvalidArgumentException(
                    '指定された商品シリーズが存在しません。'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Expected Series Type
            |--------------------------------------------------------------------------
            */

            $expectedSeriesTypes = [
                'volume'    => 'book_series',
                'quarterly' => 'quarterly',
                'monthly'   => 'monthly',
            ];

            if (!isset($expectedSeriesTypes[$productType])) {
                throw new \InvalidArgumentException(
                    'この商品種別にはシリーズを指定できません。'
                );
            }


            /*
            |--------------------------------------------------------------------------
            | Series Type Check
            |--------------------------------------------------------------------------
            */

            $seriesType = (string) (
                $series['series_type'] ?? ''
            );

            if (
                $seriesType !==
                $expectedSeriesTypes[$productType]
            ) {
                throw new \InvalidArgumentException(
                    '商品種別と所属シリーズの種別が一致していません。'
                );
            }
        }



    }