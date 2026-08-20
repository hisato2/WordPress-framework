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
                新規シリーズ登録
            </h1>

            <p class="dashboard-page__description">
                書籍シリーズ・季刊誌・月刊誌の基本情報を登録します。
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
            'hks_create_series',
            'hks_series_nonce'
        );
        ?>

        <input
            type="hidden"
            name="hks_dashboard_action"
            value="hks_create_series"
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
                        ></textarea>

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
                                        $value,
                                        'draft'
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
             Submit
             ================================================= -->

        <div class="member-detail__actions">

            <button
                type="submit"
                class="member-detail-submit"
            >
                シリーズを登録
            </button>

        </div>

    </form>

</div>