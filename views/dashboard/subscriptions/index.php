<?php

declare(strict_types=1);

defined('ABSPATH') || exit;


/*
|--------------------------------------------------------------------------
| Labels
|--------------------------------------------------------------------------
*/

$seriesTypeLabels = [
    'quarterly' => '季刊誌',
    'monthly'   => '月刊誌',
];

$statusLabels = [
    'active'   => '販売中',
    'inactive' => '停止中',
];


/*
|--------------------------------------------------------------------------
| URLs
|--------------------------------------------------------------------------
*/

$createUrl = add_query_arg(
    [
        'view' => 'subscription-create',
    ],
    home_url('/dashboard/')
);

$seriesListUrl = add_query_arg(
    [
        'view' => 'series',
    ],
    home_url('/dashboard/')
);

?>

<div class="product-management">



    <?php

    /*
    |--------------------------------------------------------------------------
    | Success Messages
    |--------------------------------------------------------------------------
    */

    $subscriptionCreated =
        isset($_GET['created'])
        && sanitize_key(
            wp_unslash($_GET['created'])
        ) === '1';

    $subscriptionUpdated =
        isset($_GET['updated'])
        && sanitize_key(
            wp_unslash($_GET['updated'])
        ) === '1';

    ?>


    <?php if ($subscriptionCreated): ?>

        <div class="member-detail-success">
            定期購読を登録しました。
        </div>

    <?php endif; ?>


    <?php if ($subscriptionUpdated): ?>

        <div class="member-detail-success">
            定期購読を更新しました。
        </div>

    <?php endif; ?>




    <header class="dashboard-page__header">

        <div>

            <p class="dashboard-page__eyebrow">
                SUBSCRIPTION MANAGEMENT
            </p>

            <h1 class="dashboard-page__title">
                定期購読管理
            </h1>

            <p class="dashboard-page__description">
                季刊誌・月刊誌の定期購読販売条件を管理します。
            </p>

        </div>




        <div class="dashboard-page__actions">

            <a
                href="<?php echo esc_url($seriesListUrl); ?>"
                class="member-list-detail-link">
                シリーズ管理へ戻る
            </a>

            <a
                href="<?php echo esc_url($createUrl); ?>"
                class="member-detail-submit">
                新規定期購読登録
            </a>

        </div>

    </header>


    <div class="member-list">

        <?php if (empty($subscriptionList)): ?>

            <div class="member-list-empty">

                <p>
                    登録されている定期購読はありません。
                </p>

            </div>

        <?php else: ?>

            <div class="member-list__table-wrap">

                <table class="member-list-table">

                    <thead>

                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">シリーズ名</th>
                            <th scope="col">種別</th>
                            <th scope="col">販売名</th>
                            <th scope="col">購読価格</th>
                            <th scope="col">購読期間</th>
                            <th scope="col">発送回数</th>
                            <th scope="col">状態</th>
                            <th scope="col">操作</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach (
                            $subscriptionList as $subscription
                        ): ?>

                            <?php

                            $subscriptionId = (int) (
                                $subscription['id'] ?? 0
                            );

                            $seriesName = (string) (
                                $subscription['series_name'] ?? ''
                            );

                            $seriesType = (string) (
                                $subscription['series_type'] ?? ''
                            );

                            $name = (string) (
                                $subscription['name'] ?? ''
                            );

                            $price = (int) (
                                $subscription['price'] ?? 0
                            );

                            $subscriptionMonths = (int) (
                                $subscription['subscription_months'] ?? 0
                            );

                            $scheduledShipments = (int) (
                                $subscription['scheduled_shipments'] ?? 0
                            );

                            $status = (string) (
                                $subscription['status'] ?? ''
                            );

                            $seriesTypeLabel =
                                $seriesTypeLabels[$seriesType]
                                ?? $seriesType;

                            $statusLabel =
                                $statusLabels[$status]
                                ?? $status;

                            $editUrl = add_query_arg(
                                [
                                    'view' => 'subscription-edit',
                                    'id'   => $subscriptionId,
                                ],
                                home_url('/dashboard/')
                            );

                            ?>

                            <tr>

                                <td>
                                    <?php echo esc_html(
                                        (string) $subscriptionId
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
                                        $name
                                    ); ?>
                                </td>

                                <td>
                                    <?php echo esc_html(
                                        number_format($price)
                                    ); ?>円
                                </td>

                                <td>
                                    <?php echo esc_html(
                                        (string) $subscriptionMonths
                                    ); ?>か月
                                </td>

                                <td>
                                    <?php echo esc_html(
                                        (string) $scheduledShipments
                                    ); ?>回
                                </td>

                                <td>

                                    <span
                                        class="member-status member-status--<?php
                                                                            echo esc_attr($status);
                                                                            ?>">
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
                                        class="member-list-detail-link">
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