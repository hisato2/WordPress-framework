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

<div class="member-detail">

    <header class="dashboard-page__header">

        <div>

            <p class="dashboard-page__eyebrow">
                SUBSCRIPTION CREATE
            </p>

            <h1 class="dashboard-page__title">
                新規定期購読登録
            </h1>

            <p class="dashboard-page__description">
                季刊誌・月刊誌の定期購読販売条件を登録します。
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



        <?php wp_nonce_field(
            'hks_create_subscription',
            'hks_subscription_nonce'
        ); ?>

        <input
            type="hidden"
            name="hks_dashboard_action"
            value="hks_create_subscription"
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

                        <label
                            for="publication_id"
                            class="member-detail__label"
                        >
                            対象刊行物
                        </label>

                        <select
                            id="publication_id"
                            name="publication_id"
                            class="member-detail__control"
                            required
                        >

                            <option value="">
                                選択してください
                            </option>

                            <?php foreach (
                                $publicationList as $publication
                            ): ?>

                                <?php

                                $publicationId = (int) (
                                    $publication['id'] ?? 0
                                );

                                $publicationName = (string) (
                                    $publication['name'] ?? ''
                                );

                                $seriesType = (string) (
                                    $publication['series_type'] ?? ''
                                );

                                $seriesTypeLabel =
                                    $seriesTypeLabels[$seriesType]
                                    ?? $seriesType;

                                ?>

                                <option
                                    value="<?php echo esc_attr(
                                        (string) $publicationId
                                    ); ?>"
                                >
                                    <?php echo esc_html(
                                        $publicationName
                                        . '（'
                                        . $seriesTypeLabel
                                        . '）'
                                    ); ?>
                                </option>

                            <?php endforeach; ?>

                        </select>

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

                            <option value="">
                                選択してください
                            </option>

                            <?php foreach (
                                $shippingPolicies as $value => $label
                            ): ?>

                                <option
                                    value="<?php echo esc_attr($value); ?>"
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

                            <option value="">
                                選択してください
                            </option>

                            <option value="active">
                                販売中
                            </option>

                            <option value="inactive">
                                停止中
                            </option>

                        </select>

                    </div>

                </div>

            </div>

        </section>


        <div class="member-detail__actions">

            <a
                href="<?php echo esc_url($listUrl); ?>"
                class="member-list-detail-link"
            >
                キャンセル
            </a>

            <button
                type="submit"
                class="member-detail-submit"
            >
                定期購読を登録
            </button>

        </div>

    </form>

</div>