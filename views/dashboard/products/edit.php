<?php

declare(strict_types=1);

defined('ABSPATH') || exit;


/*
|--------------------------------------------------------------------------
| Product Check
|--------------------------------------------------------------------------
*/

if (empty($product) || !is_array($product)) {

    wp_die(
        '指定された商品が見つかりません。',
        '商品が見つかりません',
        ['response' => 404]
    );
}


/*
|--------------------------------------------------------------------------
| Product Data
|--------------------------------------------------------------------------
*/

$productId = (int) ($product['id'] ?? 0);

$productCode = (string) ($product['product_code'] ?? '');

$productName = (string) ($product['name'] ?? '');

$productType = (string) ($product['product_type'] ?? '');

$description = (string) ($product['description'] ?? '');


/*
|--------------------------------------------------------------------------
| Product Types
|--------------------------------------------------------------------------
*/

$productTypes = [
    'book'     => '単行本',
    'volume'   => 'シリーズ本',
    'quarterly' => '季刊誌',
    'monthly'   => '月刊誌',
    'software' => 'ソフトウェア',
];


/*
|--------------------------------------------------------------------------
| URLs
|--------------------------------------------------------------------------
*/

$listUrl = home_url('/dashboard/?view=products');

?>

<div class="product-management">

    <header class="dashboard-page__header">

        <div>

            <p class="dashboard-page__eyebrow">
                PRODUCT MANAGEMENT
            </p>

            <h1 class="dashboard-page__title">
                商品編集
            </h1>

            <p class="dashboard-page__description">
                登録済みの商品情報を編集します。
            </p>

        </div>

    </header>


    <div class="member-detail">


        <form
            method="post"
            action="<?php echo esc_url(
                home_url('/dashboard/')
            ); ?>"
            enctype="multipart/form-data"
            class="member-detail__form"
        >

            <?php wp_nonce_field(
                'hks_update_product',
                'hks_product_nonce'
            ); ?>

            <input
                type="hidden"
                name="hks_dashboard_action"
                value="update_product"
            >

            <input
                type="hidden"
                name="product_id"
                value="<?php echo esc_attr((string) $productId); ?>"
            >

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
                                value="<?php echo esc_attr($productCode); ?>"
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
                                        <?php selected(
                                            $productType,
                                            $value
                                        ); ?>
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

                                <?php foreach ($seriesList as $series): ?>

                                    <?php

                                    $seriesId = (int) (
                                        $series['id'] ?? 0
                                    );

                                    $seriesName = (string) (
                                        $series['name'] ?? ''
                                    );

                                    $seriesType = (string) (
                                        $series['series_type'] ?? ''
                                    );

                                    ?>

                                    <option
                                        value="<?php echo esc_attr(
                                            (string) $seriesId
                                        ); ?>"
                                        data-series-type="<?php echo esc_attr(
                                            $seriesType
                                        ); ?>"
                                        <?php selected(
                                            (int) ($product['series_id'] ?? 0),
                                            $seriesId
                                        ); ?>
                                    >
                                        <?php echo esc_html(
                                            $seriesName
                                        ); ?>
                                    </option>

                                <?php endforeach; ?>

                            </select>

                            <p class="member-detail__help">
                                商品種別に対応するシリーズを選択してください。
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
                                value="<?php echo esc_attr($productName); ?>"
                                required
                            >

                        </div>


                        <!-- 商品説明 -->

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
                            ><?php echo esc_textarea($description); ?></textarea>

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


                     <?php if (!empty($product['image_path'])): ?>

                                <?php

                                $uploadDir = wp_upload_dir();

                                $currentImageUrl =
                                    trailingslashit((string) $uploadDir['baseurl'])
                                    . ltrim(
                                        (string) $product['image_path'],
                                        '/'
                                    );

                                ?>

                                <div class="product-image-current">

                                    <div class="product-image-current__label">
                                        現在の商品画像
                                    </div>

                                    <div class="product-image-current__preview">

                                        <img
                                            src="<?php echo esc_url($currentImageUrl); ?>"
                                            alt="<?php echo esc_attr($productName); ?>"
                                            class="product-image-current__image"
                                        >

                                    </div>

                                    <div class="product-image-current__filename">
                                        <?php echo esc_html(
                                            basename(
                                                (string) $product['image_path']
                                            )
                                        ); ?>
                                    </div>

                                </div>

                            <?php endif; ?>



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


                            <?php if (!empty($product['preview_pdf_path'])): ?>

                                <?php

                                $uploadDir = wp_upload_dir();

                                $currentPreviewPdfUrl =
                                    trailingslashit((string) $uploadDir['baseurl'])
                                    . ltrim(
                                        (string) $product['preview_pdf_path'],
                                        '/'
                                    );

                                ?>

                                <div class="product-image-current">

                                    <div class="product-image-current__label">
                                        現在のお試しPDF
                                    </div>

                                    <div class="member-detail__value">

                                        <a
                                            href="<?php echo esc_url(
                                                $currentPreviewPdfUrl
                                            ); ?>"
                                            class="member-detail-pdf-link"
                                            target="_blank"
                                            rel="noopener noreferrer"
                                        >
                                            お試しPDFを開く
                                        </a>

                                        <span class="member-detail-pdf-filename">
                                            <?php echo esc_html(
                                                basename(
                                                    (string) $product['preview_pdf_path']
                                                )
                                            ); ?>
                                        </span>                                     

                                    </div>

                                </div>

                            <?php endif; ?>


                            <input
                                type="file"
                                id="preview_pdf"
                                name="preview_pdf"
                                class="member-detail__file"
                                accept=".pdf,application/pdf"
                            >


                            <p class="member-detail__help">
                                PDF形式、最大20MB。
                                新しいPDFを選択した場合のみ、
                                現在のお試しPDFを差し替えます。
                            </p>


                            <label>
                                <input
                                    type="checkbox"
                                    id="preview_enabled"
                                    name="preview_enabled"
                                    value="1"
                                    <?php checked(
                                        (int) ($product['preview_enabled'] ?? 0),
                                        1
                                    ); ?>
                                >
                                お試しPDFを公開する
                            </label>


                            <?php if (empty($product['preview_pdf_path'])): ?>

                                <p class="member-detail__help">
                                    現在、お試しPDFは登録されていません。
                                </p>

                            <?php endif; ?>

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
                                value="<?php echo esc_attr(
                                    (string) ($product['isbn'] ?? '')
                                ); ?>"
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
                                value="<?php echo esc_attr(
                                    (string) ($product['volume_number'] ?? '')
                                ); ?>"
                            >

                        </div>


                        <!-- 号番号 -->

                        <div class="member-detail__item">

                            <label
                                for="issue_number"
                                class="member-detail__label"
                            >
                                号番号
                            </label>

                            <input
                                type="text"
                                id="issue_number"
                                name="issue_number"
                                class="member-detail__control"
                                maxlength="50"
                                value="<?php echo esc_attr(
                                    (string) ($product['issue_number'] ?? '')
                                ); ?>"
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
                                value="<?php echo esc_attr(
                                    (string) ($product['publication_year'] ?? '')
                                ); ?>"
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
                                value="<?php echo esc_attr(
                                    (string) ($product['publication_month'] ?? '')
                                ); ?>"
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
                                value="<?php echo esc_attr(
                                    (string) ($product['publication_date'] ?? '')
                                ); ?>"
                            >

                        </div>

                    </div>

                </div>

            </section>

            <!-- =================================================
                ソフトウェア情報
                ================================================= -->

            <section
                id="software_section"
                class="member-detail__section"
                style="display: none;"
            >

                <header class="member-detail__section-header">

                    <h2 class="member-detail__section-title">
                        ソフトウェア情報
                    </h2>

                </header>


                <div class="member-detail__section-body">

                    <div class="member-detail__grid">


                        <!-- ソフトウェアバージョン -->

                        <div class="member-detail__item">

                            <label
                                for="software_version"
                                class="member-detail__label"
                            >
                                ソフトウェアバージョン
                            </label>

                            <input
                                type="text"
                                id="software_version"
                                name="software_version"
                                class="member-detail__control"
                                maxlength="50"
                                value="<?php echo esc_attr(
                                    (string) ($product['software_version'] ?? '')
                                ); ?>"
                                placeholder="例: 1.0.1"
                            >

                            <p class="member-detail__help">
                                バージョン番号を入力してください。
                            </p>

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
                                value="<?php echo esc_attr(
                                    (string) ($salesOption['price'] ?? '')
                                ); ?>"
                                required
                            >

                            <p class="member-detail__help">
                                商品の単品販売価格を入力してください。
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

                            <?php

                            $currentShippingPolicy =
                                (string) (
                                    $salesOption['shipping_policy']
                                    ?? 'per_order'
                                );

                            ?>

                            <select
                                id="shipping_policy"
                                name="shipping_policy"
                                class="member-detail__control"
                                required
                            >

                                <option
                                    value="per_order"
                                    <?php selected(
                                        $currentShippingPolicy,
                                        'per_order'
                                    ); ?>
                                >
                                    注文ごと
                                </option>

                                <option
                                    value="included"
                                    <?php selected(
                                        $currentShippingPolicy,
                                        'included'
                                    ); ?>
                                >
                                    商品価格に含む
                                </option>

                                <option
                                    value="free"
                                    <?php selected(
                                        $currentShippingPolicy,
                                        'free'
                                    ); ?>
                                >
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
                                value="<?php echo esc_attr(
                                    (string) (
                                        $salesOption['shipping_amount']
                                        ?? '0'
                                    )
                                ); ?>"
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

                            <?php

                            $currentSalesStatus =
                                (string) (
                                    $salesOption['status']
                                    ?? 'active'
                                );

                            ?>

                            <select
                                id="sales_status"
                                name="sales_status"
                                class="member-detail__control"
                                required
                            >

                                <option
                                    value="active"
                                    <?php selected(
                                        $currentSalesStatus,
                                        'active'
                                    ); ?>
                                >
                                    販売中
                                </option>

                                <option
                                    value="inactive"
                                    <?php selected(
                                        $currentSalesStatus,
                                        'inactive'
                                    ); ?>
                                >
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
                                value="<?php echo esc_attr(
                                    (string) ($product['tax_rate'] ?? '10.00')
                                ); ?>"
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

                                <?php

                                $taxTypes = [
                                    'included' => '税込',
                                    'excluded' => '税抜',
                                    'exempt'   => '非課税',
                                ];

                                $currentTaxType = (string) (
                                    $product['tax_type'] ?? 'included'
                                );

                                ?>

                                <?php foreach (
                                    $taxTypes as $value => $label
                                ): ?>

                                    <option
                                        value="<?php echo esc_attr($value); ?>"
                                        <?php selected(
                                            $currentTaxType,
                                            $value
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

                                <?php

                                $statuses = [
                                    'draft'        => '下書き',
                                    'active'       => '販売中',
                                    'inactive'     => '非公開',
                                    'discontinued' => '販売終了',
                                ];

                                $currentStatus = (string) (
                                    $product['status'] ?? 'draft'
                                );

                                ?>

                                <?php foreach (
                                    $statuses as $value => $label
                                ): ?>

                                    <option
                                        value="<?php echo esc_attr($value); ?>"
                                        <?php selected(
                                            $currentStatus,
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
                    変更を保存
                </button>

            </div>


        </form>

    </div>

</div>