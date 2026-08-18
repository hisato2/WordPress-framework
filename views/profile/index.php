<?php



$successMessage = $auth->session()->getFlash('success');
$errorMessage   = $auth->session()->getFlash('error');


/**
 * 個人本人用プロフィール編集画面
 *
 * views/profile/index.php
 */

/*
|--------------------------------------------------------------------------
| Profile Data
|--------------------------------------------------------------------------
|
| $user : ログイン中の本人データ
| $auth : 認証オブジェクト
|
| 権限は表示のみ。
| パスワード変更はプロフィール更新フォームとは分離する。
|
*/

$lastName  = (string) ($user['last_name'] ?? '');
$firstName = (string) ($user['first_name'] ?? '');
$email     = (string) ($user['email'] ?? '');
$phoneNumber = (string) ($user['phone'] ?? '');
$postalCode = (string) ($user['postal_code'] ?? '');
$prefecture = (string) ($user['prefecture'] ?? '');
$city       = (string) ($user['city'] ?? '');
$address1   = (string) ($user['address1'] ?? '');
$address2   = (string) ($user['address2'] ?? '');

$roleLabel = $auth->roleLabel();


/*
|--------------------------------------------------------------------------
| Profile Image
|--------------------------------------------------------------------------
|
| DBの profile_image には相対パスが保存されている前提。
| 管理者側と同じく wp_upload_dir() から表示URLを生成する。
|
*/

$profileImageUrl = '';

if (!empty($user['profile_image'])) {

    $uploadDir = wp_upload_dir();

    $profileImageUrl =
        trailingslashit((string) $uploadDir['baseurl']) .
        ltrim((string) $user['profile_image'], '/');
}


/*
|--------------------------------------------------------------------------
| Avatar Fallback
|--------------------------------------------------------------------------
|
| プロフィール画像が未登録の場合は氏名の先頭文字を表示する。
|
*/

$avatarInitial = '';

if ($lastName !== '') {
    $avatarInitial = mb_substr($lastName, 0, 1);
} elseif ($firstName !== '') {
    $avatarInitial = mb_substr($firstName, 0, 1);
} else {
    $avatarInitial = 'U';
}


/*
|--------------------------------------------------------------------------
| Password URL
|--------------------------------------------------------------------------
|
| 本人用パスワード変更ルート確定後に接続する。
|
| 管理者用
| /dashboard/?view=user-password&id=...
|
| はここでは使用しない。
|
*/

$passwordUrl = '';

?>


<?php if ($successMessage || $errorMessage): ?>

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
                    <?= esc_html((string) $successMessage); ?>
                </span>
            </div>

        <?php endif; ?>


        <?php if ($errorMessage): ?>

            <div
                class="toast toast-error"
                role="alert"
            >
                <span>
                    <?= esc_html((string) $errorMessage); ?>
                </span>
            </div>

        <?php endif; ?>

    </div>

<?php endif; ?>





<div class="profile-page">

    <div class="profile-page__inner">

        <!-- =================================================
             Page Header
             ================================================= -->

        <header class="profile-page__header">

            <p class="profile-page__eyebrow">
                MY PROFILE
            </p>

            <h1 class="profile-page__title">
                プロフィール
            </h1>

            <p class="profile-page__description">
                登録情報の確認・変更ができます。
            </p>

        </header>


        <!-- =================================================
             User Summary
             ================================================= -->

        <section class="profile-summary">

            <div class="profile-summary__avatar">

                <?php if ($profileImageUrl !== ''): ?>

                    <img
                        src="<?= esc_url($profileImageUrl); ?>"
                        alt="<?= esc_attr(
                            trim($lastName . ' ' . $firstName)
                        ); ?>"
                        class="profile-summary__avatar-image"
                    >

                <?php else: ?>

                    <div
                        class="profile-summary__avatar-empty"
                        aria-hidden="true"
                    >
                        <?= esc_html($avatarInitial); ?>
                    </div>

                <?php endif; ?>

            </div>


            <div class="profile-summary__content">

                <h2 class="profile-summary__name">

                    <?= esc_html(
                        trim($lastName . ' ' . $firstName)
                    ); ?>

                </h2>

                <p class="profile-summary__email">
                    <?= esc_html($email); ?>
                </p>

                <span class="profile-summary__role">
                    <?= esc_html($roleLabel); ?>
                </span>

            </div>

        </section>


        <!-- =================================================
             Profile Update Form
             ================================================= -->

        <form
            method="post"
            action="<?= esc_url(home_url('/profile/')); ?>"
            enctype="multipart/form-data"
            id="profileUpdateForm"
            class="profile-form"
        >

            <input
                type="hidden"
                name="hks_profile_action"
                value="update"
            >

            <?php wp_nonce_field(
                'hks_update_profile_action',
                'hks_update_profile_nonce'
            ); ?>


            <!-- =============================================
                 Basic Information
                 ============================================= -->

            <section class="profile-section">

                <header class="profile-section__header">

                    <h2 class="profile-section__title">
                        基本情報
                    </h2>

                </header>


                <div class="profile-section__body">

                    <div class="profile-grid">

                        <!-- Last Name -->

                        <div class="profile-field">

                            <label
                                for="profile_last_name"
                                class="profile-field__label"
                            >
                                姓
                            </label>

                            <input
                                type="text"
                                id="profile_last_name"
                                name="last_name"
                                value="<?= esc_attr($lastName); ?>"
                                class="profile-field__control"
                                autocomplete="family-name"
                                required
                            >

                        </div>


                        <!-- First Name -->

                        <div class="profile-field">

                            <label
                                for="profile_first_name"
                                class="profile-field__label"
                            >
                                名
                            </label>

                            <input
                                type="text"
                                id="profile_first_name"
                                name="first_name"
                                value="<?= esc_attr($firstName); ?>"
                                class="profile-field__control"
                                autocomplete="given-name"
                                required
                            >

                        </div>


                        <!-- Email -->

                        <div class="profile-field">

                            <label
                                for="profile_email"
                                class="profile-field__label"
                            >
                                メールアドレス
                            </label>

                            <input
                                type="email"
                                id="profile_email"
                                name="email"
                                value="<?= esc_attr($email); ?>"
                                class="profile-field__control"
                                autocomplete="email"
                                required
                            >

                        </div>


                        <!-- Role : 表示のみ -->

                        <div class="profile-field">

                            <span class="profile-field__label">
                                権限
                            </span>

                            <div class="profile-field__readonly">
                                <?= esc_html($roleLabel); ?>
                            </div>

                        </div>

                    </div>

                </div>

            </section>


            <!-- =============================================
                 Address / Contact
                 ============================================= -->

            <section class="profile-section">

                <header class="profile-section__header">

                    <h2 class="profile-section__title">
                        住所・連絡先
                    </h2>

                </header>


                <div class="profile-section__body">

                    <div class="profile-grid">

                        <!-- Phone -->

                        <div class="profile-field">

                            <label
                                for="profile_phone_number"
                                class="profile-field__label"
                            >
                                電話番号
                            </label>

                            <input
                                type="text"
                                id="profile_phone_number"
                                name="phone"
                                value="<?= esc_attr($phoneNumber); ?>"
                                class="profile-field__control"
                                autocomplete="tel"
                            >

                        </div>


                        <!-- Postal Code -->

                        <div class="profile-field">

                            <label
                                for="profile_postal_code"
                                class="profile-field__label"
                            >
                                郵便番号
                            </label>

                            <input
                                type="text"
                                id="profile_postal_code"
                                name="postal_code"
                                value="<?= esc_attr($postalCode); ?>"
                                class="profile-field__control"
                                autocomplete="postal-code"
                                inputmode="numeric"
                            >

                        </div>


                        <!-- Prefecture -->

                        <div class="profile-field">

                            <label
                                for="profile_prefecture"
                                class="profile-field__label"
                            >
                                都道府県
                            </label>

                            <input
                                type="text"
                                id="profile_prefecture"
                                name="prefecture"
                                value="<?= esc_attr($prefecture); ?>"
                                class="profile-field__control"
                                autocomplete="address-level1"
                            >

                        </div>


                        <!-- City -->

                        <div class="profile-field">

                            <label
                                for="profile_city"
                                class="profile-field__label"
                            >
                                市区町村
                            </label>

                            <input
                                type="text"
                                id="profile_city"
                                name="city"
                                value="<?= esc_attr($city); ?>"
                                class="profile-field__control"
                                autocomplete="address-level2"
                            >

                        </div>


                        <!-- Address 1 -->

                        <div class="profile-field profile-field--full">

                            <label
                                for="profile_address1"
                                class="profile-field__label"
                            >
                                町名・番地
                            </label>

                            <input
                                type="text"
                                id="profile_address1"
                                name="address1"
                                value="<?= esc_attr($address1); ?>"
                                class="profile-field__control"
                                autocomplete="address-line1"
                            >

                        </div>


                        <!-- Address 2 -->

                        <div class="profile-field profile-field--full">

                            <label
                                for="profile_address2"
                                class="profile-field__label"
                            >
                                建物名・部屋番号
                            </label>

                            <input
                                type="text"
                                id="profile_address2"
                                name="address2"
                                value="<?= esc_attr($address2); ?>"
                                class="profile-field__control"
                                autocomplete="address-line2"
                            >

                        </div>

                    </div>

                </div>

            </section>


            <!-- =============================================
                 Profile Image
                 ============================================= -->

            <section class="profile-section">

                <header class="profile-section__header">

                    <h2 class="profile-section__title">
                        プロフィール画像
                    </h2>

                </header>


                <div class="profile-section__body">

                    <div class="profile-avatar-edit">

                        <div class="profile-avatar-edit__preview">

                            <?php if ($profileImageUrl !== ''): ?>

                                <img
                                    src="<?= esc_url(
                                        $profileImageUrl
                                    ); ?>"
                                    alt="<?= esc_attr(
                                        trim(
                                            $lastName .
                                            ' ' .
                                            $firstName
                                        )
                                    ); ?>"
                                    class="profile-avatar-edit__image"
                                >

                            <?php else: ?>

                                <div
                                    class="profile-avatar-edit__empty"
                                    aria-hidden="true"
                                >
                                    <?= esc_html($avatarInitial); ?>
                                </div>

                            <?php endif; ?>

                        </div>


                        <div class="profile-avatar-edit__content">

                            <label
                                for="profile_image"
                                class="profile-field__label"
                            >
                                画像を変更
                            </label>

                            <input
                                type="file"
                                id="profile_image"
                                name="profile_image"
                                accept="image/jpeg,image/png,image/webp"
                                class="profile-file-input"
                            >

                            <p class="profile-field__help">
                                JPEG・PNG・WebP形式の画像を選択してください。
                            </p>

                        </div>

                    </div>

                </div>

            </section>


            <!-- =============================================
                 Form Actions
                 ============================================= -->

            <div class="profile-actions">

                <a
                    href="<?= esc_url(home_url('/mypage/')); ?>"
                    class="profile-button profile-button--secondary"
                >
                    マイページへ戻る
                </a>

                <button
                    type="submit"
                    name="profile_update_submit"
                    value="1"
                    class="profile-button profile-button--primary"
                >
                    更新する
                </button>

            </div>

        </form>


<!-- =================================================
     Security
     ================================================= -->

<section class="profile-section profile-section--security">

    <header class="profile-section__header">

        <h2 class="profile-section__title">
            セキュリティ
        </h2>

    </header>


    <div class="profile-section__body">

        <div class="profile-security">

            <div class="profile-security__content">

                <span class="profile-field__label">
                    パスワード
                </span>

                <p class="profile-security__password">
                    ••••••••••••
                </p>

                <p class="profile-field__help">
                    セキュリティ保護のため、
                    現在のパスワードは表示されません。
                </p>

            </div>


            <div class="profile-security__action">

                <button
                    type="button"
                    class="profile-button profile-button--secondary"
                    id="passwordChangeToggle"
                    aria-expanded="false"
                    aria-controls="passwordChangeForm"
                >
                    パスワードを変更
                </button>

            </div>

        </div>

    <!-- =================================================
         Password Change Form
         ================================================= -->

    <form
        method="post"
        action="<?= esc_url(home_url('/profile/')); ?>"
        id="passwordChangeForm"
        class="profile-password-form"
        hidden
    >

        <input
            type="hidden"
            name="hks_profile_action"
            value="change_password"
        >

        <?php
        wp_nonce_field(
            'hks_change_password_action',
            'hks_change_password_nonce'
        );
        ?>


        <div class="profile-grid">

            <div class="profile-field profile-field--full">

                <label
                    for="current_password"
                    class="profile-field__label"
                >
                    現在のパスワード
                </label>

                <input
                    type="password"
                    id="current_password"
                    name="current_password"
                    class="profile-field__control"
                    autocomplete="current-password"
                    required
                >

            </div>


            <div class="profile-field">

                <label
                    for="new_password"
                    class="profile-field__label"
                >
                    新しいパスワード
                </label>

                <input
                    type="password"
                    id="new_password"
                    name="new_password"
                    class="profile-field__control"
                    autocomplete="new-password"
                    minlength="8"
                    required
                >

                <p class="profile-field__help">
                    8文字以上で入力してください。
                </p>

            </div>


            <div class="profile-field">

                <label
                    for="new_password_confirmation"
                    class="profile-field__label"
                >
                    新しいパスワード（確認）
                </label>

                <input
                    type="password"
                    id="new_password_confirmation"
                    name="new_password_confirmation"
                    class="profile-field__control"
                    autocomplete="new-password"
                    minlength="8"
                    required
                >

            </div>

        </div>


        <div class="profile-actions">

            <button
                type="button"
                class="profile-button profile-button--secondary"
                id="passwordChangeCancel"
            >
                キャンセル
            </button>

            <button
                type="submit"
                class="profile-button profile-button--primary"
            >
                パスワードを変更する
            </button>

        </div>

    </form>
        

    </div>

</section>



    </div>

</div>