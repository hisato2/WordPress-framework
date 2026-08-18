<?php

declare(strict_types=1);

defined('ABSPATH') || exit;


/* =========================================================
   URLs
   ========================================================= */

$listUrl = home_url('/dashboard/?view=users');


/* =========================================================
   Labels
   ========================================================= */

$roles = [
    'super_admin' => '最高管理者',
    'admin'       => '管理者',
    'manager'     => 'マネージャー',
    'staff'       => 'スタッフ',
    'member'      => '会員',
];

$statuses = [
    'temporary' => '仮登録',
    'active'    => '有効',
    'suspended' => '利用停止',
    'deleted'   => '退会',
];

?>

<div class="member-management">

    <!-- =====================================================
         Page Header
         ===================================================== -->

    <header class="dashboard-page__header">

        <p class="dashboard-page__eyebrow">
            MEMBER MANAGEMENT
        </p>

        <h1 class="dashboard-page__title">
            会員詳細
        </h1>

        <p class="dashboard-page__description">
            会員情報の確認・編集ができます。
        </p>

    </header>


    <?php if (empty($member)): ?>

        <!-- =================================================
             Member Not Found
             ================================================= -->

        <div class="member-list-empty">

            <p>
                指定された会員は見つかりませんでした。
            </p>

            <a
                href="<?php echo esc_url($listUrl); ?>"
                class="member-detail-back-link"
            >
                会員一覧へ戻る
            </a>

        </div>


    <?php else: ?>

        <?php

        /* =====================================================
           Member Data
           ===================================================== */

        $memberName = trim(
            (string) $member['last_name']
            . ' '
            . (string) $member['first_name']
        );

        $role = (string) ($member['role'] ?? '');

        $status = (string) ($member['status'] ?? '');

        $roleLabel = $roles[$role] ?? $role;

        $statusLabel = $statuses[$status] ?? $status;


        /* =====================================================
           Profile Image
           ===================================================== */

        $profileImageUrl = '';

        if (!empty($member['profile_image'])) {

            $uploadDir = wp_upload_dir();

            $profileImageUrl = trailingslashit(
                (string) $uploadDir['baseurl']
            ) . ltrim(
                (string) $member['profile_image'],
                '/'
            );
        }


        /* =====================================================
           Password URL
           ===================================================== */

        $passwordUrl = add_query_arg(
            [
                'view' => 'user-password',
                'id'   => $member['id'],
            ],
            home_url('/dashboard/')
        );

        ?>


        <div class="member-detail">


            <!-- =================================================
                 Profile Summary
                 ================================================= -->

            <div class="member-detail__profile">

                <div class="member-profile-image">

                    <?php if ($profileImageUrl !== ''): ?>

                        <img
                            src="<?php echo esc_url($profileImageUrl); ?>"
                            alt="<?php echo esc_attr($memberName); ?>"
                            class="member-profile-image__preview"
                        >

                    <?php else: ?>

                        <div class="member-profile-image__placeholder">
                            No Image
                        </div>

                    <?php endif; ?>

                </div>


                <div class="member-detail__profile-content">

                    <h2 class="member-detail__name">
                        <?php echo esc_html($memberName); ?>
                    </h2>

                    <p class="member-detail__email">
                        <?php echo esc_html(
                            (string) ($member['email'] ?? '')
                        ); ?>
                    </p>


                    <div class="member-detail__profile-meta">

                        <span
                            class="member-status member-status--<?php
                            echo esc_attr($status);
                            ?>"
                        >
                            <?php echo esc_html($statusLabel); ?>
                        </span>


                        <span class="member-role">
                            <?php echo esc_html($roleLabel); ?>
                        </span>

                    </div>

                </div>

            </div>


            <!-- =================================================
                 Member Edit Form
                 ================================================= -->

            <form
                method="post"
                action=""
                enctype="multipart/form-data"
                class="member-detail__form"
            >

                <input
                    type="hidden"
                    name="action"
                    value="hks_update_member_profile"
                >

                <input
                    type="hidden"
                    name="user_id"
                    value="<?php echo esc_attr(
                        (string) $member['id']
                    ); ?>"
                >


                <?php

                wp_nonce_field(
                    'hks_update_member_profile_action',
                    'hks_update_member_profile_nonce'
                );

                ?>


                <!-- =============================================
                     Basic Information
                     ============================================= -->

                <section class="member-detail__section">

                    <header class="member-detail__section-header">

                        <h2 class="member-detail__section-title">
                            基本情報
                        </h2>

                    </header>


                    <div class="member-detail__section-body">

                        <div class="member-detail__grid">


                            <!-- Member ID -->

                            <div class="member-detail__item">

                                <span class="member-detail__label">
                                    会員ID
                                </span>

                                <p class="member-detail__value">
                                    <?php echo esc_html(
                                        (string) $member['id']
                                    ); ?>
                                </p>

                            </div>


                            <!-- Last Name -->

                            <div class="member-detail__item">

                                <label
                                    for="last_name"
                                    class="member-detail__label"
                                >
                                    姓
                                </label>

                                <input
                                    type="text"
                                    id="last_name"
                                    name="last_name"
                                    value="<?php echo esc_attr(
                                        (string) (
                                            $member['last_name'] ?? ''
                                        )
                                    ); ?>"
                                    class="member-detail__control"
                                    autocomplete="family-name"
                                    required
                                >

                            </div>


                            <!-- First Name -->

                            <div class="member-detail__item">

                                <label
                                    for="first_name"
                                    class="member-detail__label"
                                >
                                    名
                                </label>

                                <input
                                    type="text"
                                    id="first_name"
                                    name="first_name"
                                    value="<?php echo esc_attr(
                                        (string) (
                                            $member['first_name'] ?? ''
                                        )
                                    ); ?>"
                                    class="member-detail__control"
                                    autocomplete="given-name"
                                    required
                                >

                            </div>


                            <!-- Email -->

                            <div class="member-detail__item">

                                <label
                                    for="email"
                                    class="member-detail__label"
                                >
                                    メールアドレス
                                </label>

                                <input
                                    type="email"
                                    id="email"
                                    name="email"
                                    value="<?php echo esc_attr(
                                        (string) (
                                            $member['email'] ?? ''
                                        )
                                    ); ?>"
                                    class="member-detail__control"
                                    autocomplete="email"
                                    required
                                >

                            </div>


                            <!-- Role -->

                            <div class="member-detail__item">

                                <label
                                    for="role"
                                    class="member-detail__label"
                                >
                                    権限
                                </label>

                                <select
                                    id="role"
                                    name="role"
                                    class="member-detail__control"
                                >

                                    <?php foreach ($roles as $value => $label): ?>

                                        <option
                                            value="<?php echo esc_attr(
                                                $value
                                            ); ?>"
                                            <?php
                                            selected(
                                                $role,
                                                $value
                                            );
                                            ?>
                                        >
                                            <?php echo esc_html($label); ?>
                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>


                            <!-- Status -->

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
                                >

                                    <?php foreach ($statuses as $value => $label): ?>

                                        <option
                                            value="<?php echo esc_attr(
                                                $value
                                            ); ?>"
                                            <?php
                                            selected(
                                                $status,
                                                $value
                                            );
                                            ?>
                                        >
                                            <?php echo esc_html($label); ?>
                                        </option>

                                    <?php endforeach; ?>

                                </select>

                            </div>


                        </div>

                    </div>

                </section>


                <!-- =============================================
                     Contact / Address
                     ============================================= -->

                <section class="member-detail__section">

                    <header class="member-detail__section-header">

                        <h2 class="member-detail__section-title">
                            住所・連絡先
                        </h2>

                    </header>


                    <div class="member-detail__section-body">

                        <div class="member-detail__grid">


                            <!-- Phone -->

                            <div class="member-detail__item">

                                <label
                                    for="phone"
                                    class="member-detail__label"
                                >
                                    電話番号
                                </label>

                                <input
                                    type="tel"
                                    id="phone"
                                    name="phone"
                                    value="<?php echo esc_attr(
                                        (string) (
                                            $member['phone'] ?? ''
                                        )
                                    ); ?>"
                                    class="member-detail__control"
                                    autocomplete="tel"
                                    placeholder="090-1234-5678"
                                >

                            </div>


                            <!-- Postal Code -->

                            <div class="member-detail__item">

                                <label
                                    for="postal_code"
                                    class="member-detail__label"
                                >
                                    郵便番号
                                </label>

                                <input
                                    type="text"
                                    id="postal_code"
                                    name="postal_code"
                                    value="<?php echo esc_attr(
                                        (string) (
                                            $member['postal_code'] ?? ''
                                        )
                                    ); ?>"
                                    class="member-detail__control"
                                    inputmode="numeric"
                                    autocomplete="postal-code"
                                    placeholder="123-4567"
                                >

                            </div>


                            <!-- Prefecture -->

                            <div class="member-detail__item">

                                <label
                                    for="prefecture"
                                    class="member-detail__label"
                                >
                                    都道府県
                                </label>

                                <input
                                    type="text"
                                    id="prefecture"
                                    name="prefecture"
                                    value="<?php echo esc_attr(
                                        (string) (
                                            $member['prefecture'] ?? ''
                                        )
                                    ); ?>"
                                    class="member-detail__control"
                                    autocomplete="address-level1"
                                    placeholder="東京都"
                                >

                            </div>


                            <!-- City -->

                            <div class="member-detail__item">

                                <label
                                    for="city"
                                    class="member-detail__label"
                                >
                                    市区町村
                                </label>

                                <input
                                    type="text"
                                    id="city"
                                    name="city"
                                    value="<?php echo esc_attr(
                                        (string) (
                                            $member['city'] ?? ''
                                        )
                                    ); ?>"
                                    class="member-detail__control"
                                    autocomplete="address-level2"
                                    placeholder="葛飾区"
                                >

                            </div>


                            <!-- Address 1 -->

                            <div
                                class="member-detail__item member-detail__item--full"
                            >

                                <label
                                    for="address1"
                                    class="member-detail__label"
                                >
                                    町名・番地
                                </label>

                                <input
                                    type="text"
                                    id="address1"
                                    name="address1"
                                    value="<?php echo esc_attr(
                                        (string) (
                                            $member['address1'] ?? ''
                                        )
                                    ); ?>"
                                    class="member-detail__control"
                                    autocomplete="address-line1"
                                    placeholder="○○町1-2-3"
                                >

                            </div>


                            <!-- Address 2 -->

                            <div
                                class="member-detail__item member-detail__item--full"
                            >

                                <label
                                    for="address2"
                                    class="member-detail__label"
                                >
                                    建物名・部屋番号
                                </label>

                                <input
                                    type="text"
                                    id="address2"
                                    name="address2"
                                    value="<?php echo esc_attr(
                                        (string) (
                                            $member['address2'] ?? ''
                                        )
                                    ); ?>"
                                    class="member-detail__control"
                                    autocomplete="address-line2"
                                    placeholder="○○ビル 101号室"
                                >

                            </div>


                        </div>

                    </div>

                </section>


                <!-- =============================================
                     Profile Image
                     ============================================= -->

                <section class="member-detail__section">

                    <header class="member-detail__section-header">

                        <h2 class="member-detail__section-title">
                            プロフィール画像
                        </h2>

                    </header>


                    <div class="member-detail__section-body">

                        <div class="member-detail__image-edit">


                            <div class="member-profile-image">

                                <?php if ($profileImageUrl !== ''): ?>

                                    <img
                                        src="<?php echo esc_url(
                                            $profileImageUrl
                                        ); ?>"
                                        alt="<?php echo esc_attr(
                                            $memberName
                                        ); ?>"
                                        class="member-profile-image__preview"
                                    >

                                <?php else: ?>

                                    <div
                                        class="member-profile-image__placeholder"
                                    >
                                        No Image
                                    </div>

                                <?php endif; ?>

                            </div>


                            <div class="member-detail__image-control">

                                <label
                                    for="profile_image"
                                    class="member-detail__label"
                                >
                                    画像を変更
                                </label>

                                <input
                                    type="file"
                                    id="profile_image"
                                    name="profile_image"
                                    accept="image/jpeg,image/png,image/webp"
                                    class="member-detail__file"
                                >

                                <p class="member-detail__help">
                                    JPEG・PNG・WebP形式の画像を選択してください。
                                </p>

                            </div>


                        </div>

                    </div>

                </section>


                <!-- =============================================
                     Form Actions
                     ============================================= -->

                <div class="member-detail__actions">

                    <a
                        href="<?php echo esc_url($listUrl); ?>"
                        class="member-detail-back-link"
                    >
                        会員一覧へ戻る
                    </a>

                    <button
                        type="submit"
                        class="member-detail-submit"
                    >
                        更新する
                    </button>

                </div>


            </form>


            <!-- =================================================
                 Security
                 Profile update formとは分離
                 ================================================= -->

            <section class="member-detail__section">

                <header class="member-detail__section-header">

                    <h2 class="member-detail__section-title">
                        セキュリティ
                    </h2>

                </header>


                <div class="member-detail__section-body">

                    <div class="member-security">

                        <div class="member-security__content">

                            <span class="member-detail__label">
                                パスワード
                            </span>

                            <p class="member-security__password">
                                ••••••••••••
                            </p>

                            <p class="member-detail__help">
                                セキュリティ保護のため、
                                現在のパスワードは表示されません。
                            </p>

                        </div>


                        <div class="member-security__action">

                            <a
                                href="<?php echo esc_url(
                                    $passwordUrl
                                ); ?>"
                                class="member-detail-back-link"
                            >
                                パスワードを変更
                            </a>

                        </div>

                    </div>

                </div>

            </section>


            <!-- =================================================
                 System Information
                 ================================================= -->

            <section class="member-detail__section">

                <header class="member-detail__section-header">

                    <h2 class="member-detail__section-title">
                        システム情報
                    </h2>

                </header>


                <div class="member-detail__section-body">

                    <div class="member-detail__grid">


                        <!-- Last Login -->

                        <div class="member-detail__item">

                            <span class="member-detail__label">
                                最終ログイン
                            </span>

                            <p class="member-detail__value">

                                <?php

                                echo esc_html(
                                    !empty($member['last_login_at'])
                                        ? (string) $member['last_login_at']
                                        : '-'
                                );

                                ?>

                            </p>

                        </div>


                        <!-- Created At -->

                        <div class="member-detail__item">

                            <span class="member-detail__label">
                                登録日時
                            </span>

                            <p class="member-detail__value">

                                <?php

                                echo esc_html(
                                    !empty($member['created_at'])
                                        ? (string) $member['created_at']
                                        : '-'
                                );

                                ?>

                            </p>

                        </div>


                        <!-- Updated At -->

                        <div class="member-detail__item">

                            <span class="member-detail__label">
                                更新日時
                            </span>

                            <p class="member-detail__value">

                                <?php

                                echo esc_html(
                                    !empty($member['updated_at'])
                                        ? (string) $member['updated_at']
                                        : '-'
                                );

                                ?>

                            </p>

                        </div>


                    </div>

                </div>

            </section>


            <!-- =================================================
                 Bottom Navigation
                 ================================================= -->

            <div class="member-detail__bottom">

                <a
                    href="<?php echo esc_url($listUrl); ?>"
                    class="member-detail-back-link"
                >
                    ← 会員一覧へ戻る
                </a>

            </div>


        </div>

    <?php endif; ?>

</div>
