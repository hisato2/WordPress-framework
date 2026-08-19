<?php

declare(strict_types=1);

defined('ABSPATH') || exit;


/*
|--------------------------------------------------------------------------
| URLs
|--------------------------------------------------------------------------
*/

$listUrl = home_url('/dashboard/?view=products');


/*
|--------------------------------------------------------------------------
| Product Types
|--------------------------------------------------------------------------
*/

$productTypes = [
    'book'     => '単行本',
    'volume'   => 'シリーズ巻',
    'issue'    => '雑誌・刊行物',
    'software' => 'ソフトウェア',
];


/*
|--------------------------------------------------------------------------
| Tax Types
|--------------------------------------------------------------------------
*/

$taxTypes = [
    'included' => '税込',
    'excluded' => '税抜',
    'exempt'   => '非課税',
];


/*
|--------------------------------------------------------------------------
| Statuses
|--------------------------------------------------------------------------
*/

$statuses = [
    'draft'        => '下書き',
    'active'       => '販売中',
    'inactive'     => '非公開',
    'discontinued' => '販売終了',
];

?>

<div class="product-management">

    <header class="dashboard-page__header">

        <div>

            <p class="dashboard-page__eyebrow">
                PRODUCT MANAGEMENT
            </p>

            <h1 class="dashboard-page__title">
                新規商品登録
            </h1>

            <p class="dashboard-page__description">
                書籍・刊行物・ソフトウェア商品を登録します。
            </p>

        </div>

    </header>


    <div class="member-detail">

        <form
            method="post"
            action=""
            enctype="multipart/form-data"
            class="member-detail__form"
        >

            <input
                type="hidden"
                name="hks_dashboard_action"
                value="hks_create_product"
            >

            <?php

            wp_nonce_field(
                'hks_create_product_action',
                'hks_create_product_nonce'
            );

            ?>


            <!-- =================================================
                 基本情報
                 ================================================= -->

            <section class="member-detail__section">

                <header class="member-detail__section-header">

                    <h2 class="member-detail__section-title">
                        基本情報
                    </h2>

                </header>


                <div class="member-detail__section-body">

                    <div class="member-detail__grid">


                        <!-- 商品コード -->

                        <div class="member-detail__item">

                            <label
                                for="product_code"
                                class="member-detail__label"
                            >
                                商品コード
                            </label>

                            <input
                                type="text"
                                id="product_code"
                                name="product_code"
                                class="member-detail__control"
                                maxlength="50"
                                required
                            >

                        </div>


                        <!-- 商品種別 -->

                        <div class="member-detail__item">

                            <label
                                for="product_type"
                                class="member-detail__label"
                            >
                                商品種別
                            </label>

                            <select
                                id="product_type"
                                name="product_type"
                                class="member-detail__control"
                                required
                            >

                                <?php foreach (
                                    $productTypes as $value => $label
                                ): ?>

                                    <option
                                        value="<?php echo esc_attr($value); ?>"
                                    >
                                        <?php echo esc_html($label); ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>



                                                <!-- 所属シリーズ -->

                        <div class="member-detail__item">

                            <label
                                for="series_id"
                                class="member-detail__label"
                            >
                                所属シリーズ
                            </label>

                            <select
                                id="series_id"
                                name="series_id"
                                class="member-detail__control"
                            >

                                <option value="">
                                    シリーズなし
                                </option>

                                <?php foreach (
                                    $seriesList as $series
                                ): ?>

                                    <?php

                                    $seriesId = (int) (
                                        $series['id'] ?? 0
                                    );

                                    $seriesName = (string) (
                                        $series['name'] ?? ''
                                    );

                                    $seriesCode = (string) (
                                        $series['series_code'] ?? ''
                                    );

                                    ?>

                                    <option
                                        value="<?php echo esc_attr(
                                            (string) $seriesId
                                        ); ?>"
                                    >
                                        <?php echo esc_html(
                                            $seriesName
                                            . (
                                                $seriesCode !== ''
                                                    ? ' [' . $seriesCode . ']'
                                                    : ''
                                            )
                                        ); ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                            <p class="member-detail__help">
                                第1巻・第2巻や各号など、
                                シリーズに属する商品の場合に選択します。
                                単独の商品は「シリーズなし」のままで構いません。
                            </p>

                        </div>
                        









                        <!-- 商品名 -->

                        <div
                            class="member-detail__item member-detail__item--full"
                        >

                            <label
                                for="name"
                                class="member-detail__label"
                            >
                                商品名
                            </label>

                            <input
                                type="text"
                                id="name"
                                name="product_name"
                                class="member-detail__control"
                                maxlength="255"
                                required
                            >

                        </div>


                        <!-- 説明 -->

                        <div
                            class="member-detail__item member-detail__item--full"
                        >

                            <label
                                for="description"
                                class="member-detail__label"
                            >
                                商品説明
                            </label>

                            <textarea
                                id="description"
                                name="description"
                                class="member-detail__control"
                                rows="6"
                            ></textarea>

                        </div>

                    </div>

                </div>

            </section>


            <!-- =================================================
                商品メディア
                ================================================= -->

            <section class="member-detail__section">

                <header class="member-detail__section-header">

                    <h2 class="member-detail__section-title">
                        商品メディア
                    </h2>

                </header>


                <div class="member-detail__section-body">

                    <div class="member-detail__grid">


                        <!-- 商品画像 -->

                        <div
                            class="member-detail__item member-detail__item--full"
                        >

                            <label
                                for="product_image"
                                class="member-detail__label"
                                id="product_image_label"
                            >
                                商品画像
                            </label>

                            <input
                                type="file"
                                id="product_image"
                                name="product_image"
                                class="member-detail__file"
                                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                            >

                            <div
                                id="product_image_help"
                                class="member-detail__help"
                            >
                                書籍・刊行物：推奨 600 × 800 px（3:4）<br>
                                JPEG / PNG / WebP、最大5MB
                            </div>

                        </div>


                        <!-- お試しPDF -->

                        <div
                            id="preview_pdf_area"
                            class="member-detail__item member-detail__item--full"
                        >

                            <label
                                for="preview_pdf"
                                class="member-detail__label"
                            >
                                お試しPDF
                            </label>

                            <input
                                type="file"
                                id="preview_pdf"
                                name="preview_pdf"
                                class="member-detail__file"
                                accept=".pdf,application/pdf"
                            >

                            <p class="member-detail__help">
                                PDF形式、最大20MB。<br>
                                管理者が作成したお試し用PDFを
                                アップロードしてください。
                                1商品につき1ファイルです。
                            </p>


                            <label>
                                <input
                                    type="checkbox"
                                    id="preview_enabled"
                                    name="preview_enabled"
                                    value="1"
                                >
                                お試しPDFを公開する
                            </label>

                        </div>

                    </div>

                </div>

            </section>


            <!-- =================================================
                 書籍・刊行情報
                 ================================================= -->

            <section
                id="publication_section"
                class="member-detail__section"
            >

                <header class="member-detail__section-header">

                    <h2 class="member-detail__section-title">
                        書籍・刊行情報
                    </h2>

                </header>


                <div class="member-detail__section-body">

                    <div class="member-detail__grid">


                        <!-- ISBN -->

                        <div class="member-detail__item">

                            <label
                                for="isbn"
                                class="member-detail__label"
                            >
                                ISBN
                            </label>

                            <input
                                type="text"
                                id="isbn"
                                name="isbn"
                                class="member-detail__control"
                                maxlength="20"
                            >

                        </div>


                        <!-- 巻番号 -->

                        <div class="member-detail__item">

                            <label
                                for="volume_number"
                                class="member-detail__label"
                            >
                                巻番号
                            </label>

                            <input
                                type="text"
                                id="volume_number"
                                name="volume_number"
                                class="member-detail__control"
                                maxlength="50"
                            >

                        </div>


                        <!-- 号番号 -->

                        <div class="member-detail__item">

                            <label
                                for="issue_number"
                                class="member-detail__label"
                            >
                                通し号番号
                            </label>

                            <input
                                type="text"
                                id="issue_number"
                                name="issue_number"
                                class="member-detail__control"
                                maxlength="50"
                            >

                        </div>


                        <!-- 発行年 -->

                        <div class="member-detail__item">

                            <label
                                for="publication_year"
                                class="member-detail__label"
                            >
                                発行年
                            </label>

                            <input
                                type="number"
                                id="publication_year"
                                name="publication_year"
                                class="member-detail__control"
                                min="1"
                                max="9999"
                            >

                        </div>


                        <!-- 発行月 -->

                        <div class="member-detail__item">

                            <label
                                for="publication_month"
                                class="member-detail__label"
                            >
                                発行月
                            </label>

                            <input
                                type="number"
                                id="publication_month"
                                name="publication_month"
                                class="member-detail__control"
                                min="1"
                                max="12"
                            >

                        </div>


                        <!-- 発行日 -->

                        <div class="member-detail__item">

                            <label
                                for="publication_date"
                                class="member-detail__label"
                            >
                                発行日
                            </label>

                            <input
                                type="date"
                                id="publication_date"
                                name="publication_date"
                                class="member-detail__control"
                            >

                        </div>

                    </div>

                </div>

            </section>


            <!-- =================================================
                 販売設定
                 ================================================= -->

            <section class="member-detail__section">

                <header class="member-detail__section-header">

                    <h2 class="member-detail__section-title">
                        販売設定
                    </h2>

                </header>


                <div class="member-detail__section-body">

                    <div class="member-detail__grid">


                        <!-- 販売価格 -->

                        <div class="member-detail__item">

                            <label
                                for="sales_price"
                                class="member-detail__label"
                            >
                                販売価格（円）
                            </label>

                            <input
                                type="number"
                                id="sales_price"
                                name="sales_price"
                                class="member-detail__control"
                                min="0"
                                step="1"
                                required
                            >

                            <p class="member-detail__help">
                                商品の単品販売価格を税込金額で入力してください。
                            </p>

                        </div>


                        <!-- 送料計算方式 -->

                        <div class="member-detail__item">

                            <label
                                for="shipping_policy"
                                class="member-detail__label"
                            >
                                送料計算方式
                            </label>

                            <select
                                id="shipping_policy"
                                name="shipping_policy"
                                class="member-detail__control"
                                required
                            >

                                <option value="per_order" selected>
                                    注文ごと
                                </option>

                                <option value="included">
                                    商品価格に含む
                                </option>

                                <option value="free">
                                    送料無料
                                </option>

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
                                value="0"
                                required
                            >

                        </div>


                        <!-- 販売状態 -->

                        <div class="member-detail__item">

                            <label
                                for="sales_status"
                                class="member-detail__label"
                            >
                                販売状態
                            </label>

                            <select
                                id="sales_status"
                                name="sales_status"
                                class="member-detail__control"
                                required
                            >

                                <option value="active" selected>
                                    販売中
                                </option>

                                <option value="inactive">
                                    販売停止
                                </option>

                            </select>

                        </div>


                    </div>

                </div>

            </section>



            <!-- =================================================
                 税・販売状態
                 ================================================= -->

            <section class="member-detail__section">

                <header class="member-detail__section-header">

                    <h2 class="member-detail__section-title">
                        税・販売状態
                    </h2>

                </header>


                <div class="member-detail__section-body">

                    <div class="member-detail__grid">


                        <!-- 消費税率 -->

                        <div class="member-detail__item">

                            <label
                                for="tax_rate"
                                class="member-detail__label"
                            >
                                消費税率（%）
                            </label>

                            <input
                                type="number"
                                id="tax_rate"
                                name="tax_rate"
                                class="member-detail__control"
                                min="0"
                                max="100"
                                step="0.01"
                                value="10.00"
                                required
                            >

                        </div>


                        <!-- 税区分 -->

                        <div class="member-detail__item">

                            <label
                                for="tax_type"
                                class="member-detail__label"
                            >
                                税区分
                            </label>

                            <select
                                id="tax_type"
                                name="tax_type"
                                class="member-detail__control"
                                required
                            >

                                <?php foreach (
                                    $taxTypes as $value => $label
                                ): ?>

                                    <option
                                        value="<?php echo esc_attr($value); ?>"
                                        <?php selected(
                                            $value,
                                            'included'
                                        ); ?>
                                    >
                                        <?php echo esc_html($label); ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                        </div>


                        <!-- ステータス -->

                        <div class="member-detail__item">

                            <label
                                for="status"
                                class="member-detail__label"
                            >
                                ステータス
                            </label>

                            <select
                                id="status"
                                name="status"
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
                 Actions
                 ================================================= -->

            <div class="member-detail__actions">

                <a
                    href="<?php echo esc_url($listUrl); ?>"
                    class="member-detail-back-link"
                >
                    商品一覧へ戻る
                </a>

                <button
                    type="submit"
                    class="member-detail-submit"
                >
                    商品を登録する
                </button>

            </div>

        </form>

    </div>

</div>