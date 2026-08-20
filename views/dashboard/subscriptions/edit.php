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

$shippingPolicies = [
    'included'     => '購読価格に送料を含む',
    'annual_fixed' => '固定送料',
];

$statusLabels = [
    'active'   => '販売中',
    'inactive' => '停止中',
];


/*
|--------------------------------------------------------------------------
| Values
|--------------------------------------------------------------------------
*/

$subscriptionId = (int) (
    $subscription['id'] ?? 0
);

$seriesName = (string) (
    $publication['name'] ?? ''
);

$seriesType = (string) (
    $publication['series_type'] ?? ''
);

$seriesTypeLabel =
    $seriesTypeLabels[$seriesType]
    ?? $seriesType;

$subscriptionName = (string) (
    $subscription['name'] ?? ''
);

$subscriptionPrice = (string) (
    $subscription['price'] ?? ''
);

$subscriptionMonths = (string) (
    $subscription['subscription_months'] ?? ''
);

$scheduledShipments = (string) (
    $subscription['scheduled_shipments'] ?? ''
);

$shippingPolicy = (string) (
    $subscription['shipping_policy'] ?? ''
);

$shippingAmount = (string) (
    $subscription['shipping_amount'] ?? ''
);

$subscriptionStatus = (string) (
    $subscription['status'] ?? 'inactive'
);


/*
|--------------------------------------------------------------------------
| URLs
|--------------------------------------------------------------------------
*/

$listUrl = add_query_arg(
    [
        'view' => 'subscriptions',
    ],
    home_url('/dashboard/')
);

?>

<div class="product-management">

    <header class="dashboard-page__header">

        <div>

            <p class="dashboard-page__eyebrow">
                SUBSCRIPTION EDIT
            </p>

            <h1 class="dashboard-page__title">
                定期購読編集
            </h1>

            <p class="dashboard-page__description">
                定期購読の販売条件を変更します。
            </p>

        </div>

        <div class="dashboard-page__actions">

            <a
                href="<?php echo esc_url($listUrl); ?>"
                class="member-list-detail-link"
            >
                定期購読一覧へ戻る
            </a>

        </div>

    </header>


    <form
        method="post"
        action="<?php echo esc_url(home_url('/dashboard/')); ?>"
        class="member-detail"
    >

        <?php
        wp_nonce_field(
            'hks_update_subscription',
            'hks_subscription_nonce'
        );
        ?>

        <input
            type="hidden"
            name="hks_dashboard_action"
            value="hks_update_subscription"
        >

        <input
            type="hidden"
            name="subscription_id"
            value="<?php echo esc_attr(
                (string) $subscriptionId
            ); ?>"
        >


        <section class="member-detail__section">

            <header class="member-detail__section-header">

                <h2 class="member-detail__section-title">
                    定期購読情報
                </h2>

            </header>


            <div class="member-detail__section-body">

                <div class="member-detail__grid">


                    <!-- 対象刊行物 -->

                    <div
                        class="
                            member-detail__item
                            member-detail__item--full
                        "
                    >

                        <label class="member-detail__label">
                            対象刊行物
                        </label>

                        <input
                            type="text"
                            class="member-detail__control"
                            value="<?php echo esc_attr(
                                $seriesName
                                . '（'
                                . $seriesTypeLabel
                                . '）'
                            ); ?>"
                            readonly
                        >

                    </div>


                    <!-- 販売名 -->

                    <div class="member-detail__item">

                        <label
                            for="subscription_name"
                            class="member-detail__label"
                        >
                            販売名
                        </label>

                        <input
                            type="text"
                            id="subscription_name"
                            name="subscription_name"
                            class="member-detail__control"
                            maxlength="255"
                            value="<?php echo esc_attr(
                                $subscriptionName
                            ); ?>"
                            required
                        >

                    </div>


                    <!-- 購読価格 -->

                    <div class="member-detail__item">

                        <label
                            for="subscription_price"
                            class="member-detail__label"
                        >
                            購読価格（円）
                        </label>

                        <input
                            type="number"
                            id="subscription_price"
                            name="subscription_price"
                            class="member-detail__control"
                            min="0"
                            step="1"
                            value="<?php echo esc_attr(
                                $subscriptionPrice
                            ); ?>"
                            required
                        >

                    </div>


                    <!-- 購読期間 -->

                    <div class="member-detail__item">

                        <label
                            for="subscription_months"
                            class="member-detail__label"
                        >
                            購読期間（月）
                        </label>

                        <input
                            type="number"
                            id="subscription_months"
                            name="subscription_months"
                            class="member-detail__control"
                            min="1"
                            step="1"
                            value="<?php echo esc_attr(
                                $subscriptionMonths
                            ); ?>"
                            required
                        >

                    </div>


                    <!-- 発送予定回数 -->

                    <div class="member-detail__item">

                        <label
                            for="scheduled_shipments"
                            class="member-detail__label"
                        >
                            発送予定回数
                        </label>

                        <input
                            type="number"
                            id="scheduled_shipments"
                            name="scheduled_shipments"
                            class="member-detail__control"
                            min="1"
                            step="1"
                            value="<?php echo esc_attr(
                                $scheduledShipments
                            ); ?>"
                            required
                        >

                    </div>


                    <!-- 送料方式 -->

                    <div class="member-detail__item">

                        <label
                            for="shipping_policy"
                            class="member-detail__label"
                        >
                            送料方式
                        </label>

                        <select
                            id="shipping_policy"
                            name="shipping_policy"
                            class="member-detail__control"
                            required
                        >

                            <?php foreach (
                                $shippingPolicies as $value => $label
                            ): ?>

                                <option
                                    value="<?php echo esc_attr($value); ?>"
                                    <?php selected(
                                        $shippingPolicy,
                                        $value
                                    ); ?>
                                >
                                    <?php echo esc_html($label); ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <!-- 送料設定額 -->

                    <div class="member-detail__item">

                        <label
                            for="shipping_amount"
                            class="member-detail__label"
                        >
                            送料設定額（円）
                        </label>

                        <input
                            type="number"
                            id="shipping_amount"
                            name="shipping_amount"
                            class="member-detail__control"
                            min="0"
                            step="1"
                            value="<?php echo esc_attr(
                                $shippingAmount
                            ); ?>"
                        >

                    </div>


                    <!-- 販売状態 -->

                    <div class="member-detail__item">

                        <label
                            for="sales_status"
                            class="member-detail__label"
                        >
                            定期購読販売状態
                        </label>

                        <select
                            id="sales_status"
                            name="sales_status"
                            class="member-detail__control"
                            required
                        >

                            <?php foreach (
                                $statusLabels as $value => $label
                            ): ?>

                                <option
                                    value="<?php echo esc_attr($value); ?>"
                                    <?php selected(
                                        $subscriptionStatus,
                                        $value
                                    ); ?>
                                >
                                    <?php echo esc_html($label); ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>

                </div>

            </div>

        </section>


        <div class="member-detail__actions">

            <button
                type="submit"
                class="member-detail-submit"
            >
                定期購読を更新
            </button>

        </div>

    </form>

</div>