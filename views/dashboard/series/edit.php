<?php

declare(strict_types=1);

defined('ABSPATH') || exit;


/*
|--------------------------------------------------------------------------
| Labels
|--------------------------------------------------------------------------
*/

$seriesTypes = [
    'book_series' => '書籍シリーズ',
    'quarterly'   => '季刊誌',
    'monthly'     => '月刊誌',
];

$statuses = [
    'draft'        => '下書き',
    'active'       => '公開中',
    'inactive'     => '非公開',
    'discontinued' => '刊行終了',
];

$shippingPolicies = [
    'included'     => '購読価格に送料を含む',
    'per_shipment' => '発送ごとに送料を加算',
    'annual_fixed' => '購読期間全体で固定送料',
    'free'         => '送料無料',
];


/*
|--------------------------------------------------------------------------
| Series Values
|--------------------------------------------------------------------------
*/

$seriesId = (int) (
    $series['id'] ?? 0
);

$seriesCode = (string) (
    $series['series_code'] ?? ''
);

$seriesType = (string) (
    $series['series_type'] ?? ''
);

$seriesName = (string) (
    $series['name'] ?? ''
);

$description = (string) (
    $series['description'] ?? ''
);

$issn = (string) (
    $series['issn'] ?? ''
);

$seriesStatus = (string) (
    $series['status'] ?? 'draft'
);


/*
|--------------------------------------------------------------------------
| Subscription Values
|--------------------------------------------------------------------------
|
| 定期購読条件が存在する場合のみDBの値を表示する。
|
| 定期購読条件が存在しないシリーズについては、
| 「12か月」などの新規登録用初期値を表示せず、
| 未設定であることが分かるよう入力欄を空にする。
|
*/

$subscriptionExists =
    is_array($subscription)
    && !empty($subscription);

$subscriptionStatus = $subscriptionExists
    ? (string) ($subscription['status'] ?? 'inactive')
    : 'inactive';

$subscriptionEnabled =
    $subscriptionExists
    && $subscriptionStatus === 'active';

$subscriptionName = $subscriptionExists
    ? (string) ($subscription['name'] ?? '')
    : '';

$subscriptionPrice = $subscriptionExists
    ? (string) ($subscription['price'] ?? '')
    : '';

$subscriptionMonths = $subscriptionExists
    ? (string) ($subscription['subscription_months'] ?? '')
    : '';

$scheduledShipments = $subscriptionExists
    ? (string) ($subscription['scheduled_shipments'] ?? '')
    : '';

$shippingPolicy = $subscriptionExists
    ? (string) ($subscription['shipping_policy'] ?? 'included')
    : 'included';

$shippingAmount = $subscriptionExists
    ? (string) ($subscription['shipping_amount'] ?? '')
    : '';

/*
|--------------------------------------------------------------------------
| URLs
|--------------------------------------------------------------------------
*/

$seriesListUrl = add_query_arg(
    [
        'view' => 'series',
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
                シリーズ編集
            </h1>

            <p class="dashboard-page__description">
                シリーズ基本情報と定期購読条件を編集します。
            </p>

        </div>

        <div class="dashboard-page__actions">

            <a
                href="<?php echo esc_url($seriesListUrl); ?>"
                class="member-list-detail-link"
            >
                シリーズ一覧へ戻る
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
            'hks_update_series',
            'hks_series_nonce'
        );
        ?>

        <input
            type="hidden"
            name="hks_dashboard_action"
            value="hks_update_series"
        >

        <input
            type="hidden"
            name="series_id"
            value="<?php echo esc_attr((string) $seriesId); ?>"
        >


        <!-- =================================================
             シリーズ基本情報
             ================================================= -->

        <section class="member-detail__section">

            <header class="member-detail__section-header">

                <h2 class="member-detail__section-title">
                    シリーズ基本情報
                </h2>

            </header>


            <div class="member-detail__section-body">

                <div class="member-detail__grid">


                    <!-- シリーズコード -->

                    <div class="member-detail__item">

                        <label
                            for="series_code"
                            class="member-detail__label"
                        >
                            シリーズコード
                        </label>

                        <input
                            type="text"
                            id="series_code"
                            name="series_code"
                            class="member-detail__control"
                            maxlength="50"
                            value="<?php echo esc_attr($seriesCode); ?>"
                            required
                        >

                    </div>


                    <!-- シリーズ種別 -->

                    <div class="member-detail__item">

                        <label
                            for="series_type"
                            class="member-detail__label"
                        >
                            シリーズ種別
                        </label>

                        <select
                            id="series_type"
                            name="series_type"
                            class="member-detail__control"
                            required
                        >

                            <?php foreach (
                                $seriesTypes as $value => $label
                            ): ?>

                                <option
                                    value="<?php echo esc_attr($value); ?>"
                                    <?php selected(
                                        $seriesType,
                                        $value
                                    ); ?>
                                >
                                    <?php echo esc_html($label); ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

                    </div>


                    <!-- シリーズ名 -->

                    <div
                        class="
                            member-detail__item
                            member-detail__item--full
                        "
                    >

                        <label
                            for="series_name"
                            class="member-detail__label"
                        >
                            シリーズ名
                        </label>

                        <input
                            type="text"
                            id="series_name"
                            name="series_name"
                            class="member-detail__control"
                            maxlength="255"
                            value="<?php echo esc_attr($seriesName); ?>"
                            required
                        >

                    </div>


                    <!-- 説明 -->

                    <div
                        class="
                            member-detail__item
                            member-detail__item--full
                        "
                    >

                        <label
                            for="description"
                            class="member-detail__label"
                        >
                            シリーズ説明
                        </label>

                        <textarea
                            id="description"
                            name="description"
                            class="member-detail__control"
                            rows="6"
                        ><?php echo esc_textarea($description); ?></textarea>

                    </div>


                    <!-- ISSN -->

                    <div class="member-detail__item">

                        <label
                            for="issn"
                            class="member-detail__label"
                        >
                            ISSN
                        </label>

                        <input
                            type="text"
                            id="issn"
                            name="issn"
                            class="member-detail__control"
                            maxlength="20"
                            value="<?php echo esc_attr($issn); ?>"
                        >

                    </div>


                    <!-- 状態 -->

                    <div class="member-detail__item">

                        <label
                            for="series_status"
                            class="member-detail__label"
                        >
                            シリーズ状態
                        </label>

                        <select
                            id="series_status"
                            name="series_status"
                            class="member-detail__control"
                            required
                        >

                            <?php foreach (
                                $statuses as $value => $label
                            ): ?>

                                <option
                                    value="<?php echo esc_attr($value); ?>"
                                    <?php selected(
                                        $seriesStatus,
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


        <!-- =================================================
             定期購読設定
             ================================================= -->

        <section class="member-detail__section">

            <header class="member-detail__section-header">

                <h2 class="member-detail__section-title">
                    定期購読設定
                </h2>

            </header>


            <div class="member-detail__section-body">

                <div class="member-detail__grid">


                    <!-- 定期購読を販売する -->

                    <div
                        class="
                            member-detail__item
                            member-detail__item--full
                        "
                    >

                        <label class="member-detail__checkbox-label">

                            <input
                                type="checkbox"
                                name="subscription_enabled"
                                value="1"
                                <?php checked(
                                    $subscriptionEnabled
                                ); ?>
                            >

                            定期購読を販売する

                        </label>

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


                    <!-- 送料 -->

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
                        >

                            <option
                                value="active"
                                <?php selected(
                                    $subscriptionStatus,
                                    'active'
                                ); ?>
                            >
                                販売中
                            </option>

                            <option
                                value="inactive"
                                <?php selected(
                                    $subscriptionStatus,
                                    'inactive'
                                ); ?>
                            >
                                停止中
                            </option>

                        </select>

                    </div>

                </div>


                <p class="member-detail__help">
                    実際の購読開始号・終了号は、購入時に購買者ごとの購読契約として管理します。
                    ここでは価格・購読期間・発送予定回数などの販売条件を設定します。
                </p>

            </div>

        </section>


        <!-- =================================================
             Submit
             ================================================= -->

        <div class="member-detail__actions">

            <button
                type="submit"
                class="member-detail-submit"
            >
                シリーズを更新
            </button>

        </div>

    </form>

</div>