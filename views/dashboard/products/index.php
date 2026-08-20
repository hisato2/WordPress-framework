<?php

declare(strict_types=1);

defined('ABSPATH') || exit;


/*
|--------------------------------------------------------------------------
| Labels
|--------------------------------------------------------------------------
*/

$productTypeLabels = [
    'book'     => '単行本',
    'volume'   => 'シリーズ本',
    'quarterly' => '季刊誌',
    'monthly'   => '月刊誌',
    'software' => 'ソフトウェア',
];

$statusLabels = [
    'draft'        => '下書き',
    'active'       => '販売中',
    'inactive'     => '非公開',
    'discontinued' => '販売終了',
];

$taxTypeLabels = [
    'included' => '税込',
    'excluded' => '税抜',
    'exempt'   => '非課税',
];


/*
|--------------------------------------------------------------------------
| URLs
|--------------------------------------------------------------------------
*/

$createUrl = add_query_arg(
    [
        'view' => 'product-create',
    ],
    home_url('/dashboard/')
);

?>

<div class="product-management">

    <header class="dashboard-page__header">

        <div>

            <p class="dashboard-page__eyebrow">
                PRODUCT MANAGEMENT
            </p>

            <h1 class="dashboard-page__title">
                商品管理
            </h1>

            <p class="dashboard-page__description">
                書籍・刊行物・ソフトウェア商品の確認・管理ができます。
            </p>

        </div>

        <div class="dashboard-page__actions">

            <a
                href="<?php echo esc_url($createUrl); ?>"
                class="member-detail-submit"
            >
                新規商品登録
            </a>

        </div>

    </header>


    <div class="member-list">

        <?php if (empty($products)): ?>

            <div class="member-list-empty">

                <p>
                    登録されている商品はありません。
                </p>

            </div>

        <?php else: ?>

            <div class="member-list__table-wrap">

                <table class="member-list-table">

                    <thead>

                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">商品コード</th>
                            <th scope="col">商品名</th>
                            <th scope="col">種別</th>
                            <th scope="col">ISBN</th>
                            <th scope="col">価格区分</th>
                            <th scope="col">状態</th>
                            <th scope="col">操作</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($products as $product): ?>

                            <?php

                            $productId = (int) ($product['id'] ?? 0);

                            $productCode = (string) (
                                $product['product_code'] ?? ''
                            );

                            $productName = (string) (
                                $product['name'] ?? ''
                            );

                            $productType = (string) (
                                $product['product_type'] ?? ''
                            );

                            $isbn = !empty($product['isbn'])
                                ? (string) $product['isbn']
                                : '-';

                            $taxType = (string) (
                                $product['tax_type'] ?? ''
                            );

                            $status = (string) (
                                $product['status'] ?? ''
                            );

                            $productTypeLabel =
                                $productTypeLabels[$productType]
                                ?? $productType;

                            $taxTypeLabel =
                                $taxTypeLabels[$taxType]
                                ?? $taxType;

                            $statusLabel =
                                $statusLabels[$status]
                                ?? $status;

                            $editUrl = add_query_arg(
                                [
                                    'view' => 'product-edit',
                                    'id'   => $productId,
                                ],
                                home_url('/dashboard/')
                            );

                            ?>

                            <tr>

                                <td>
                                    <?php echo esc_html(
                                        (string) $productId
                                    ); ?>
                                </td>

                                <td>
                                    <?php echo esc_html(
                                        $productCode
                                    ); ?>
                                </td>

                                <td>
                                    <strong>
                                        <?php echo esc_html(
                                            $productName
                                        ); ?>
                                    </strong>
                                </td>

                                <td>
                                    <?php echo esc_html(
                                        $productTypeLabel
                                    ); ?>
                                </td>

                                <td>
                                    <?php echo esc_html($isbn); ?>
                                </td>

                                <td>
                                    <?php echo esc_html(
                                        $taxTypeLabel
                                    ); ?>
                                </td>

                                <td>

                                    <span
                                        class="member-status member-status--<?php
                                        echo esc_attr($status);
                                        ?>"
                                    >
                                        <?php echo esc_html(
                                            $statusLabel
                                        ); ?>
                                    </span>

                                </td>

                                <td>

                                    <a
                                        href="<?php echo esc_url(
                                            $editUrl
                                        ); ?>"
                                        class="member-list-detail-link"
                                    >
                                        編集
                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </div>

</div>