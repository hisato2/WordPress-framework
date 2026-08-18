<?php

declare(strict_types=1);

$resetToken = isset($_GET['token'])
    ? sanitize_text_field(wp_unslash($_GET['token']))
    : '';
?>

<section class="auth">

    <div class="auth-container">

        <div class="card auth-card">

            <div class="card-header auth-header">

                <h1 class="card-title auth-title">
                    パスワード再設定
                </h1>

            </div>

            <div class="card-body">

                <?php if ($resetToken === '') : ?>

                    <div class="message message--error">
                        パスワード再設定トークンがありません。
                    </div>

                <?php else : ?>

                    <p class="auth-description">
                        新しいパスワードを入力してください。
                    </p>

                    <form
                        id="reset-password-form"
                        class="auth-form"
                    >

                        <input
                            type="hidden"
                            name="token"
                            value="<?= esc_attr($resetToken); ?>"
                        >

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= esc_attr((new \HKS\Auth\Token())->getOrGenerate()); ?>"
                        >

                        <div class="form-group">

                            <label
                                for="password"
                                class="form-label"
                            >
                                新しいパスワード
                            </label>

                            <input
                                class="form-control"
                                type="password"
                                id="password"
                                name="password"
                                required
                                autocomplete="new-password"
                            >

                        </div>

                        <div class="form-group">

                            <label
                                for="password_confirmation"
                                class="form-label"
                            >
                                新しいパスワード（確認）
                            </label>

                            <input
                                class="form-control"
                                type="password"
                                id="password_confirmation"
                                name="password_confirmation"
                                required
                                autocomplete="new-password"
                            >

                        </div>

                        <div class="form-actions auth-actions">

                            <button
                                type="submit"
                                class="btn btn-primary btn-block"
                            >
                                パスワードを変更
                            </button>

                        </div>

                    </form>

                <?php endif; ?>

            </div>

            <div class="card-footer auth-links">

                <p>

                    <a href="<?= esc_url(home_url('/login/')); ?>">
                        ログイン画面へ戻る
                    </a>

                </p>

            </div>

        </div>

    </div>

</section>