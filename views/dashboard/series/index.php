<?php

declare(strict_types=1);

defined('ABSPATH') || exit;


/*
|--------------------------------------------------------------------------
| Labels
|--------------------------------------------------------------------------
*/

$seriesTypeLabels = [
    'book_series' => '書籍シリーズ',
    'quarterly'   => '季刊誌',
    'monthly'     => '月刊誌',
];

$statusLabels = [
    'draft'        => '下書き',
    'active'       => '公開中',
    'inactive'     => '非公開',
    'discontinued' => '刊行終了',
];


/*
|--------------------------------------------------------------------------
| URLs
|--------------------------------------------------------------------------
*/

$createUrl = add_query_arg(
    [
        'view' => 'series-create',
    ],
    home_url('/dashboard/')
);

$productListUrl = add_query_arg(
    [
        'view' => 'products',
    ],
    home_url('/dashboard/')
);

?>

<div class="product-management">

    <header class="dashboard-page__header">

        <div>

            <p class="dashboard-page__eyebrow">
                SERIES MANAGEMENT
            </p>

            <h1 class="dashboard-page__title">
                シリーズ管理
            </h1>

            <p class="dashboard-page__description">
                書籍シリーズ・季刊誌・月刊誌・定期購読を管理します。
            </p>

        </div>

        <div class="dashboard-page__actions">

            <a
                href="<?php echo esc_url($productListUrl); ?>"
                class="member-list-detail-link"
            >
                商品管理へ戻る
            </a>

            <a
                href="<?php echo esc_url($createUrl); ?>"
                class="member-detail-submit"
            >
                新規シリーズ登録
            </a>

        </div>

    </header>


    <div class="member-list">

        <?php if (empty($seriesList)): ?>

            <div class="member-list-empty">

                <p>
                    登録されているシリーズはありません。
                </p>

            </div>

        <?php else: ?>

            <div class="member-list__table-wrap">

                <table class="member-list-table">

                    <thead>

                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">シリーズコード</th>
                            <th scope="col">シリーズ名</th>
                            <th scope="col">種別</th>
                            <th scope="col">ISSN</th>
                            <th scope="col">状態</th>
                            <th scope="col">操作</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($seriesList as $series): ?>

                            <?php

                            $seriesId = (int) (
                                $series['id'] ?? 0
                            );

                            $seriesCode = (string) (
                                $series['series_code'] ?? ''
                            );

                            $seriesName = (string) (
                                $series['name'] ?? ''
                            );

                            $seriesType = (string) (
                                $series['series_type'] ?? ''
                            );

                            $issn = !empty($series['issn'])
                                ? (string) $series['issn']
                                : '-';

                            $status = (string) (
                                $series['status'] ?? ''
                            );

                            $seriesTypeLabel =
                                $seriesTypeLabels[$seriesType]
                                ?? $seriesType;

                            $statusLabel =
                                $statusLabels[$status]
                                ?? $status;

                            $editUrl = add_query_arg(
                                [
                                    'view' => 'series-edit',
                                    'id'   => $seriesId,
                                ],
                                home_url('/dashboard/')
                            );

                            ?>

                            <tr>

                                <td>
                                    <?php echo esc_html(
                                        (string) $seriesId
                                    ); ?>
                                </td>

                                <td>
                                    <?php echo esc_html(
                                        $seriesCode
                                    ); ?>
                                </td>

                                <td>
                                    <strong>
                                        <?php echo esc_html(
                                            $seriesName
                                        ); ?>
                                    </strong>
                                </td>

                                <td>
                                    <?php echo esc_html(
                                        $seriesTypeLabel
                                    ); ?>
                                </td>

                                <td>
                                    <?php echo esc_html(
                                        $issn
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