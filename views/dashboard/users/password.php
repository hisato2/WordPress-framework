<?php

declare(strict_types=1);


/*
|--------------------------------------------------------------------------
| URLs
|--------------------------------------------------------------------------
*/

$listUrl = add_query_arg(
    [
        'view' => 'users',
    ],
    home_url('/dashboard/')
);


/*
|--------------------------------------------------------------------------
| Member Not Found
|--------------------------------------------------------------------------
*/

if (empty($member)) {
    ?>

    <header class="dashboard-page__header">

        <p class="dashboard-page__eyebrow">
            MEMBER MANAGEMENT
        </p>

        <h1 class="dashboard-page__title">
            パスワード変更
        </h1>

        <p class="dashboard-page__description">
            会員のログインパスワードを変更します。
        </p>

    </header>


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

    <?php

    return;
}


/*
|--------------------------------------------------------------------------
| Member Data
|--------------------------------------------------------------------------
*/

$memberId = (int) ($member['id'] ?? 0);

$memberName = trim(
    (string) ($member['last_name'] ?? '')
    . ' '
    . (string) ($member['first_name'] ?? '')
);

$memberEmail = (string) ($member['email'] ?? '');


/*
|--------------------------------------------------------------------------
| URLs
|--------------------------------------------------------------------------
*/

$detailUrl = add_query_arg(
    [
        'view' => 'user-detail',
        'id'   => $memberId,
    ],
    home_url('/dashboard/')
);


/*
|--------------------------------------------------------------------------
| Flash Message
|--------------------------------------------------------------------------
*/

$successMessage = $auth->session()->getFlash('success');

$errorMessage = $auth->session()->getFlash('error');

?>





<!-- =====================================================
     Flash Message
     ===================================================== -->

<div
    class="toast-container"
    aria-live="polite"
    aria-atomic="true"
>

    <?php if ($successMessage): ?>

        <div
            class="toast toast-success"
            role="status"
        >
            <span>
                <?php echo esc_html(
                    (string) $successMessage
                ); ?>
            </span>
        </div>

    <?php endif; ?>


    <?php if ($errorMessage): ?>

        <div
            class="toast toast-error"
            role="alert"
        >
            <span>
                <?php echo esc_html(
                    (string) $errorMessage
                ); ?>
            </span>
        </div>

    <?php endif; ?>

</div>


<!-- =====================================================
     Page Header
     ===================================================== -->

<header class="dashboard-page__header">

    <p class="dashboard-page__eyebrow">
        MEMBER MANAGEMENT
    </p>

    <h1 class="dashboard-page__title">
        パスワード変更
    </h1>

    <p class="dashboard-page__description">
        会員のログインパスワードを変更します。
    </p>

</header>


<div class="member-detail">


    <!-- =================================================
         Member Information
         ================================================= -->

    <section class="member-detail__section">

        <header class="member-detail__section-header">

            <h2 class="member-detail__section-title">
                対象会員
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
                            (string) $memberId
                        ); ?>
                    </p>

                </div>


                <!-- Member Name -->

                <div class="member-detail__item">

                    <span class="member-detail__label">
                        氏名
                    </span>

                    <p class="member-detail__value">
                        <?php echo esc_html(
                            $memberName
                        ); ?>
                    </p>

                </div>


                <!-- Email -->

                <div
                    class="
                        member-detail__item
                        member-detail__item--full
                    "
                >

                    <span class="member-detail__label">
                        メールアドレス
                    </span>

                    <p class="member-detail__value">
                        <?php echo esc_html(
                            $memberEmail
                        ); ?>
                    </p>

                </div>


            </div>

        </div>

    </section>


    <!-- =================================================
         Password Change Form
         ================================================= -->

    <form
        method="post"
        action="<?php echo esc_url(
            home_url('/dashboard/')
        ); ?>"
        class="member-detail__form"
    >

        <input
            type="hidden"
            name="hks_dashboard_action"
            value="change_member_password"
        >

        <input
            type="hidden"
            name="user_id"
            value="<?php echo esc_attr(
                (string) $memberId
            ); ?>"
        >


        <?php

        wp_nonce_field(
            'hks_admin_change_member_password_action',
            'hks_admin_change_member_password_nonce'
        );

        ?>


        <section class="member-detail__section">

            <header class="member-detail__section-header">

                <h2 class="member-detail__section-title">
                    新しいパスワード
                </h2>

            </header>


            <div class="member-detail__section-body">

                <div class="member-detail__grid">


                    <!-- New Password -->

                    <div class="member-detail__item">

                        <label
                            for="new_password"
                            class="member-detail__label"
                        >
                            新しいパスワード
                        </label>

                        <input
                            type="password"
                            id="new_password"
                            name="new_password"
                            class="member-detail__control"
                            autocomplete="new-password"
                            minlength="8"
                            required
                        >

                        <p class="member-detail__help">
                            8文字以上で入力してください。
                        </p>

                    </div>


                    <!-- New Password Confirmation -->

                    <div class="member-detail__item">

                        <label
                            for="new_password_confirmation"
                            class="member-detail__label"
                        >
                            新しいパスワード（確認）
                        </label>

                        <input
                            type="password"
                            id="new_password_confirmation"
                            name="new_password_confirmation"
                            class="member-detail__control"
                            autocomplete="new-password"
                            minlength="8"
                            required
                        >

                    </div>


                </div>

            </div>

        </section>


        <!-- =================================================
             Form Actions
             ================================================= -->

        <div class="member-detail__actions">

            <a
                href="<?php echo esc_url($detailUrl); ?>"
                class="member-detail-back-link"
            >
                キャンセル
            </a>

            <button
                type="submit"
                class="member-detail-submit"
            >
                パスワードを変更する
            </button>

        </div>


    </form>


    <!-- =================================================
         Bottom Navigation
         ================================================= -->

    <div class="member-detail__bottom">

        <a
            href="<?php echo esc_url($detailUrl); ?>"
            class="member-detail-back-link"
        >
            ← 会員詳細へ戻る
        </a>

    </div>


</div>