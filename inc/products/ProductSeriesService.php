<?php

declare(strict_types=1);

namespace HKS\Products;

use HKS\Repositories\ProductSeriesRepository;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Product Series Service
 *
 * 商品シリーズに関する
 * 入力検証・業務ロジックを担当する。
 *
 * 対象:
 * - 書籍シリーズ
 * - 季刊誌
 * - 月刊誌
 */
final class ProductSeriesService
{
    /**
     * Product Series Repository
     */
    private ProductSeriesRepository $repository;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->repository =
            new ProductSeriesRepository();
    }


    /**
     * シリーズ登録
     *
     * @param array<string, mixed> $data
     * @return int
     */
    public function create(array $data): int
    {
        /*
        |--------------------------------------------------------------------------
        | Normalize
        |--------------------------------------------------------------------------
        */

        $data = $this->normalize(
            $data
        );


        /*
        |--------------------------------------------------------------------------
        | Validate
        |--------------------------------------------------------------------------
        */

        $this->validate(
            $data
        );


        /*
        |--------------------------------------------------------------------------
        | シリーズコード重複確認
        |--------------------------------------------------------------------------
        */

        if (
            $this->repository->existsBySeriesCode(
                $data['series_code']
            )
        ) {
            throw new \InvalidArgumentException(
                'このシリーズコードは既に使用されています。'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Create
        |--------------------------------------------------------------------------
        */

        $seriesId =
            $this->repository->create(
                $data
            );


        if (
            $seriesId === null
            || $seriesId <= 0
        ) {
            throw new \RuntimeException(
                'シリーズを登録できませんでした。'
            );
        }


        return $seriesId;
    }


    /**
     * シリーズ更新
     *
     * @param int $seriesId
     * @param array<string, mixed> $data
     */
    public function update(
        int $seriesId,
        array $data
    ): void {

        /*
        |--------------------------------------------------------------------------
        | Series ID
        |--------------------------------------------------------------------------
        */

        if ($seriesId <= 0) {
            throw new \InvalidArgumentException(
                'シリーズIDが正しくありません。'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Existing Series
        |--------------------------------------------------------------------------
        */

        $existing =
            $this->repository->findById(
                $seriesId
            );


        if ($existing === null) {
            throw new \InvalidArgumentException(
                '指定されたシリーズが見つかりません。'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Normalize
        |--------------------------------------------------------------------------
        */

        $data = $this->normalize(
            $data
        );


        /*
        |--------------------------------------------------------------------------
        | Validate
        |--------------------------------------------------------------------------
        */

        $this->validate(
            $data
        );


        /*
        |--------------------------------------------------------------------------
        | シリーズコード重複確認
        |--------------------------------------------------------------------------
        */

        if (
            $this->repository->existsBySeriesCodeExceptId(
                $data['series_code'],
                $seriesId
            )
        ) {
            throw new \InvalidArgumentException(
                'このシリーズコードは既に使用されています。'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Update
        |--------------------------------------------------------------------------
        */

        $result =
            $this->repository->update(
                $seriesId,
                $data
            );


        if (!$result) {
            throw new \RuntimeException(
                'シリーズを更新できませんでした。'
            );
        }
    }


    /**
     * 入力値を正規化する。
     *
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    private function normalize(
        array $data
    ): array {

        return [

            'series_code' => isset($data['series_code'])
                ? trim((string) $data['series_code'])
                : '',

            'series_type' => isset($data['series_type'])
                ? trim((string) $data['series_type'])
                : '',

            'name' => isset($data['name'])
                ? trim((string) $data['name'])
                : '',

            'description' => isset($data['description'])
                ? trim((string) $data['description'])
                : '',

            'issn' => isset($data['issn'])
                ? trim((string) $data['issn'])
                : '',

            'status' => isset($data['status'])
                ? trim((string) $data['status'])
                : 'draft',
        ];
    }


    /**
     * 入力値を検証する。
     *
     * @param array<string, mixed> $data
     */
    private function validate(
        array $data
    ): void {

        /*
        |--------------------------------------------------------------------------
        | シリーズコード
        |--------------------------------------------------------------------------
        */

        if ($data['series_code'] === '') {
            throw new \InvalidArgumentException(
                'シリーズコードを入力してください。'
            );
        }


        if (
            mb_strlen(
                $data['series_code']
            ) > 50
        ) {
            throw new \InvalidArgumentException(
                'シリーズコードは50文字以内で入力してください。'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | シリーズ種別
        |--------------------------------------------------------------------------
        */

        $allowedSeriesTypes = [
            'book_series',
            'quarterly',
            'monthly',
        ];


        if (
            !in_array(
                $data['series_type'],
                $allowedSeriesTypes,
                true
            )
        ) {
            throw new \InvalidArgumentException(
                'シリーズ種別が正しくありません。'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | シリーズ名
        |--------------------------------------------------------------------------
        */

        if ($data['name'] === '') {
            throw new \InvalidArgumentException(
                'シリーズ名を入力してください。'
            );
        }


        if (
            mb_strlen(
                $data['name']
            ) > 255
        ) {
            throw new \InvalidArgumentException(
                'シリーズ名は255文字以内で入力してください。'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | ISSN
        |--------------------------------------------------------------------------
        */

        if (
            $data['issn'] !== ''
            && mb_strlen(
                $data['issn']
            ) > 20
        ) {
            throw new \InvalidArgumentException(
                'ISSNは20文字以内で入力してください。'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Status
        |--------------------------------------------------------------------------
        */

        $allowedStatuses = [
            'draft',
            'active',
            'inactive',
            'discontinued',
        ];


        if (
            !in_array(
                $data['status'],
                $allowedStatuses,
                true
            )
        ) {
            throw new \InvalidArgumentException(
                'シリーズ状態が正しくありません。'
            );
        }
    }
}