<?php

declare(strict_types=1);

namespace HKS\Products;

use HKS\Repositories\SalesOptionRepository;

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Sales Option Service
 *
 * 商品・シリーズの販売条件に関する
 * 業務ロジックを担当する。
 *
 * 現段階では主に商品の単品販売条件を扱う。
 */
final class SalesOptionService
{
    /**
     * Sales Option Repository
     */
    private SalesOptionRepository $salesOptionRepository;


    public function __construct()
    {
        $this->salesOptionRepository =
            new SalesOptionRepository();
    }


    /**
     * 商品の単品販売条件を登録する。
     *
     * @param int $productId
     * @param array<string, mixed> $data
     * @return int
     */
    public function createSingleForProduct(
        int $productId,
        array $data
    ): int {

        /*
        |----------------------------------------------------------------------
        | Product ID
        |----------------------------------------------------------------------
        */

        if ($productId <= 0) {
            throw new \InvalidArgumentException(
                '商品IDが正しくありません。'
            );
        }


        /*
        |----------------------------------------------------------------------
        | Duplicate Check
        |----------------------------------------------------------------------
        |
        | 現段階では、1商品につき
        | single の販売条件は1件とする。
        |
        */

        $existing =
            $this->salesOptionRepository
                ->findSingleByProductId($productId);

        if ($existing !== null) {
            throw new \InvalidArgumentException(
                'この商品にはすでに単品販売条件が登録されています。'
            );
        }


        /*
        |----------------------------------------------------------------------
        | Price
        |----------------------------------------------------------------------
        */

        $price = $this->normalizeUnsignedInteger(
            $data['price'] ?? null,
            '販売価格'
        );


        /*
        |----------------------------------------------------------------------
        | Name
        |----------------------------------------------------------------------
        */

        $name = isset($data['name'])
            ? trim((string) $data['name'])
            : '';

        if ($name === '') {
            $name = '単品購入';
        }

        if (mb_strlen($name) > 255) {
            throw new \InvalidArgumentException(
                '販売形態名は255文字以内で入力してください。'
            );
        }


        /*
        |----------------------------------------------------------------------
        | Shipping Policy
        |----------------------------------------------------------------------
        */

        $shippingPolicy = isset($data['shipping_policy'])
            ? (string) $data['shipping_policy']
            : 'per_order';

        $allowedShippingPolicies = [
            'per_order',
            'included',
            'per_shipment',
            'annual_fixed',
            'free',
        ];

        if (
            !in_array(
                $shippingPolicy,
                $allowedShippingPolicies,
                true
            )
        ) {
            throw new \InvalidArgumentException(
                '送料計算方式が正しくありません。'
            );
        }


        /*
        |----------------------------------------------------------------------
        | Shipping Amount
        |----------------------------------------------------------------------
        */

        $shippingAmount =
            $this->normalizeUnsignedInteger(
                $data['shipping_amount'] ?? 0,
                '送料'
            );


        /*
        |----------------------------------------------------------------------
        | Status
        |----------------------------------------------------------------------
        */

        $status = isset($data['status'])
            ? (string) $data['status']
            : 'active';

        if (
            !in_array(
                $status,
                [
                    'active',
                    'inactive',
                ],
                true
            )
        ) {
            throw new \InvalidArgumentException(
                '販売状態が正しくありません。'
            );
        }


        /*
        |----------------------------------------------------------------------
        | Create
        |----------------------------------------------------------------------
        */

        $salesOptionId =
            $this->salesOptionRepository->create(
                [
                    'product_id'            => $productId,
                    'series_id'             => null,
                    'sales_type'            => 'single',
                    'name'                  => $name,
                    'price'                 => $price,
                    'subscription_months'   => null,
                    'scheduled_shipments'   => null,
                    'shipping_policy'       => $shippingPolicy,
                    'shipping_amount'       => $shippingAmount,
                    'status'                => $status,
                ]
            );

        if ($salesOptionId === null) {
            throw new \RuntimeException(
                '販売条件を登録できませんでした。'
            );
        }

        return $salesOptionId;
    }


    /**
     * 商品の単品販売条件を更新する。
     *
     * @param int $productId
     * @param array<string, mixed> $data
     */
    public function updateSingleForProduct(
        int $productId,
        array $data
    ): void {

        if ($productId <= 0) {
            throw new \InvalidArgumentException(
                '商品IDが正しくありません。'
            );
        }


        /*
        |----------------------------------------------------------------------
        | Existing Sales Option
        |----------------------------------------------------------------------
        */

        $existing =
            $this->salesOptionRepository
                ->findSingleByProductId($productId);

        if ($existing === null) {
            throw new \InvalidArgumentException(
                'この商品の単品販売条件が見つかりません。'
            );
        }


        /*
        |----------------------------------------------------------------------
        | Price
        |----------------------------------------------------------------------
        */

        $price = $this->normalizeUnsignedInteger(
            $data['price'] ?? null,
            '販売価格'
        );


        /*
        |----------------------------------------------------------------------
        | Name
        |----------------------------------------------------------------------
        */

        $name = isset($data['name'])
            ? trim((string) $data['name'])
            : '';

        if ($name === '') {
            $name = '単品購入';
        }

        if (mb_strlen($name) > 255) {
            throw new \InvalidArgumentException(
                '販売形態名は255文字以内で入力してください。'
            );
        }


        /*
        |----------------------------------------------------------------------
        | Shipping Policy
        |----------------------------------------------------------------------
        */

        $shippingPolicy = isset($data['shipping_policy'])
            ? (string) $data['shipping_policy']
            : 'per_order';

        $allowedShippingPolicies = [
            'per_order',
            'included',
            'per_shipment',
            'annual_fixed',
            'free',
        ];

        if (
            !in_array(
                $shippingPolicy,
                $allowedShippingPolicies,
                true
            )
        ) {
            throw new \InvalidArgumentException(
                '送料計算方式が正しくありません。'
            );
        }


        /*
        |----------------------------------------------------------------------
        | Shipping Amount
        |----------------------------------------------------------------------
        */

        $shippingAmount =
            $this->normalizeUnsignedInteger(
                $data['shipping_amount'] ?? 0,
                '送料'
            );


        /*
        |----------------------------------------------------------------------
        | Status
        |----------------------------------------------------------------------
        */

        $status = isset($data['status'])
            ? (string) $data['status']
            : 'active';

        if (
            !in_array(
                $status,
                [
                    'active',
                    'inactive',
                ],
                true
            )
        ) {
            throw new \InvalidArgumentException(
                '販売状態が正しくありません。'
            );
        }


        /*
        |----------------------------------------------------------------------
        | Update
        |----------------------------------------------------------------------
        */

        $updated =
            $this->salesOptionRepository->update(
                (int) $existing['id'],
                [
                    'name'            => $name,
                    'price'           => $price,
                    'shipping_policy' => $shippingPolicy,
                    'shipping_amount' => $shippingAmount,
                    'status'          => $status,
                ]
            );

        if (!$updated) {
            throw new \RuntimeException(
                '販売条件を更新できませんでした。'
            );
        }
    }



    /**
     * シリーズの定期購読販売条件を登録する。
     *
     * 現段階では、
     * 1シリーズにつき subscription は1件とする。
     *
     * @param int $seriesId
     * @param array<string, mixed> $data
     * @return int
     */
    public function createSubscriptionForSeries(
        int $seriesId,
        array $data
    ): int {

        /*
        |----------------------------------------------------------------------
        | Series ID
        |----------------------------------------------------------------------
        */

        if ($seriesId <= 0) {
            throw new \InvalidArgumentException(
                'シリーズIDが正しくありません。'
            );
        }


        /*
        |----------------------------------------------------------------------
        | Duplicate Check
        |----------------------------------------------------------------------
        */

        $existing =
            $this->salesOptionRepository
                ->findSubscriptionBySeriesId(
                    $seriesId
                );

        if ($existing !== null) {
            throw new \InvalidArgumentException(
                'このシリーズにはすでに定期購読条件が登録されています。'
            );
        }


        /*
        |----------------------------------------------------------------------
        | Price
        |----------------------------------------------------------------------
        */

        $price =
            $this->normalizeUnsignedInteger(
                $data['price'] ?? null,
                '定期購読価格'
            );


        /*
        |----------------------------------------------------------------------
        | Name
        |----------------------------------------------------------------------
        */

        $name = isset($data['name'])
            ? trim((string) $data['name'])
            : '';

        if ($name === '') {
            $name = '定期購読';
        }

        if (mb_strlen($name) > 255) {
            throw new \InvalidArgumentException(
                '販売形態名は255文字以内で入力してください。'
            );
        }


        /*
        |----------------------------------------------------------------------
        | Subscription Months
        |----------------------------------------------------------------------
        */

        $subscriptionMonths =
            $this->normalizePositiveInteger(
                $data['subscription_months'] ?? null,
                '購読期間'
            );


        /*
        |----------------------------------------------------------------------
        | Scheduled Shipments
        |----------------------------------------------------------------------
        */

        $scheduledShipments =
            $this->normalizePositiveInteger(
                $data['scheduled_shipments'] ?? null,
                '予定発送回数'
            );


        /*
        |----------------------------------------------------------------------
        | Shipping Policy
        |----------------------------------------------------------------------
        */

        $shippingPolicy = isset($data['shipping_policy'])
            ? (string) $data['shipping_policy']
            : 'included';

        $allowedShippingPolicies = [
            'per_order',
            'included',
            'per_shipment',
            'annual_fixed',
            'free',
        ];

        if (
            !in_array(
                $shippingPolicy,
                $allowedShippingPolicies,
                true
            )
        ) {
            throw new \InvalidArgumentException(
                '送料計算方式が正しくありません。'
            );
        }


        /*
        |----------------------------------------------------------------------
        | Shipping Amount
        |----------------------------------------------------------------------
        */

        $shippingAmount =
            $this->normalizeUnsignedInteger(
                $data['shipping_amount'] ?? 0,
                '送料'
            );


        /*
        |----------------------------------------------------------------------
        | Status
        |----------------------------------------------------------------------
        */

        $status = isset($data['status'])
            ? (string) $data['status']
            : 'active';

        if (
            !in_array(
                $status,
                [
                    'active',
                    'inactive',
                ],
                true
            )
        ) {
            throw new \InvalidArgumentException(
                '販売状態が正しくありません。'
            );
        }


        /*
        |----------------------------------------------------------------------
        | Create
        |----------------------------------------------------------------------
        */

        $salesOptionId =
            $this->salesOptionRepository->create(
                [
                    'product_id'          => null,
                    'series_id'           => $seriesId,
                    'sales_type'          => 'subscription',
                    'name'                => $name,
                    'price'               => $price,
                    'subscription_months' => $subscriptionMonths,
                    'scheduled_shipments' => $scheduledShipments,
                    'shipping_policy'     => $shippingPolicy,
                    'shipping_amount'     => $shippingAmount,
                    'status'              => $status,
                ]
            );

        if ($salesOptionId === null) {
            throw new \RuntimeException(
                '定期購読条件を登録できませんでした。'
            );
        }

        return $salesOptionId;
    }

    /**
     * シリーズの定期購読販売条件を更新する。
     *
     * @param int $seriesId
     * @param array<string, mixed> $data
     */
    public function updateSubscriptionForSeries(
        int $seriesId,
        array $data
    ): void {

        /*
        |----------------------------------------------------------------------
        | Series ID
        |----------------------------------------------------------------------
        */

        if ($seriesId <= 0) {
            throw new \InvalidArgumentException(
                'シリーズIDが正しくありません。'
            );
        }


        /*
        |----------------------------------------------------------------------
        | Existing Sales Option
        |----------------------------------------------------------------------
        */

        $existing =
            $this->salesOptionRepository
                ->findSubscriptionBySeriesId(
                    $seriesId
                );

        if ($existing === null) {
            throw new \InvalidArgumentException(
                'このシリーズの定期購読条件が見つかりません。'
            );
        }


        /*
        |----------------------------------------------------------------------
        | Price
        |----------------------------------------------------------------------
        */

        $price =
            $this->normalizeUnsignedInteger(
                $data['price'] ?? null,
                '定期購読価格'
            );


        /*
        |----------------------------------------------------------------------
        | Name
        |----------------------------------------------------------------------
        */

        $name = isset($data['name'])
            ? trim((string) $data['name'])
            : '';

        if ($name === '') {
            $name = '定期購読';
        }

        if (mb_strlen($name) > 255) {
            throw new \InvalidArgumentException(
                '販売形態名は255文字以内で入力してください。'
            );
        }


        /*
        |----------------------------------------------------------------------
        | Subscription Months
        |----------------------------------------------------------------------
        */

        $subscriptionMonths =
            $this->normalizePositiveInteger(
                $data['subscription_months'] ?? null,
                '購読期間'
            );


        /*
        |----------------------------------------------------------------------
        | Scheduled Shipments
        |----------------------------------------------------------------------
        */

        $scheduledShipments =
            $this->normalizePositiveInteger(
                $data['scheduled_shipments'] ?? null,
                '予定発送回数'
            );


        /*
        |----------------------------------------------------------------------
        | Shipping Policy
        |----------------------------------------------------------------------
        */

        $shippingPolicy = isset($data['shipping_policy'])
            ? (string) $data['shipping_policy']
            : 'included';

        $allowedShippingPolicies = [
            'per_order',
            'included',
            'per_shipment',
            'annual_fixed',
            'free',
        ];

        if (
            !in_array(
                $shippingPolicy,
                $allowedShippingPolicies,
                true
            )
        ) {
            throw new \InvalidArgumentException(
                '送料計算方式が正しくありません。'
            );
        }


        /*
        |----------------------------------------------------------------------
        | Shipping Amount
        |----------------------------------------------------------------------
        */

        $shippingAmount =
            $this->normalizeUnsignedInteger(
                $data['shipping_amount'] ?? 0,
                '送料'
            );


        /*
        |----------------------------------------------------------------------
        | Status
        |----------------------------------------------------------------------
        */

        $status = isset($data['status'])
            ? (string) $data['status']
            : 'active';

        if (
            !in_array(
                $status,
                [
                    'active',
                    'inactive',
                ],
                true
            )
        ) {
            throw new \InvalidArgumentException(
                '販売状態が正しくありません。'
            );
        }


        /*
        |----------------------------------------------------------------------
        | Update
        |----------------------------------------------------------------------
        */

        $updated =
            $this->salesOptionRepository->update(
                (int) $existing['id'],
                [
                    'name'                => $name,
                    'price'               => $price,
                    'subscription_months' => $subscriptionMonths,
                    'scheduled_shipments' => $scheduledShipments,
                    'shipping_policy'     => $shippingPolicy,
                    'shipping_amount'     => $shippingAmount,
                    'status'              => $status,
                ]
            );

        if (!$updated) {
            throw new \RuntimeException(
                '定期購読条件を更新できませんでした。'
            );
        }
    }

    
    /**
     * 1以上の整数へ正規化する。
     *
     * 購読期間・予定発送回数など、
     * 0を許可しない値に使用する。
     *
     * @param mixed $value
     */
    private function normalizePositiveInteger(
        mixed $value,
        string $label
    ): int {

        if (
            $value === null
            || $value === ''
        ) {
            throw new \InvalidArgumentException(
                $label . 'を入力してください。'
            );
        }

        $value = trim((string) $value);

        if (
            $value === ''
            || !ctype_digit($value)
        ) {
            throw new \InvalidArgumentException(
                $label . 'は1以上の整数で入力してください。'
            );
        }

        $number = (int) $value;

        if ($number <= 0) {
            throw new \InvalidArgumentException(
                $label . 'は1以上の整数で入力してください。'
            );
        }

        return $number;
    }




    /**
     * 0以上の整数へ正規化する。
     *
     * price / shipping_amount は
     * DB上 unsigned int のため負数を許可しない。
     *
     * @param mixed $value
     */
    private function normalizeUnsignedInteger(
        mixed $value,
        string $label
    ): int {

        if (
            $value === null
            || $value === ''
        ) {
            throw new \InvalidArgumentException(
                $label . 'を入力してください。'
            );
        }

        $value = trim((string) $value);

        if (
            $value === ''
            || !ctype_digit($value)
        ) {
            throw new \InvalidArgumentException(
                $label . 'は0以上の整数で入力してください。'
            );
        }

        $number = (int) $value;

        if ($number < 0) {
            throw new \InvalidArgumentException(
                $label . 'は0以上の整数で入力してください。'
            );
        }

        return $number;
    }
}
