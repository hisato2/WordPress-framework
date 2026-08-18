<?php

declare(strict_types=1);
?>

<section class="auth">

    <div class="auth-container">

        <div class="card auth-card">

            <div class="card-header auth-header">

                <h1 class="card-title auth-title">
                    パスワードを忘れた方
                </h1>

            </div>

            <div class="card-body">

                <?php if (isset($error) && $error !== '') : ?>

                    <div class="message message--error">

                        <?= esc_html($error); ?>

                    </div>

                <?php endif; ?>

                <?php if (isset($success) && $success !== '') : ?>

                    <div class="message message--success">

                        <?= esc_html($success); ?>

                    </div>

                <?php endif; ?>

                <p class="auth-description">
                    登録されているメールアドレスを入力してください。<br>
                    パスワード再設定用のご案内をお送りします。
                </p>

                <form
                    id="forgot-password-form"
                    class="auth-form"
                >

                    <div class="form-group">

                        <label
                            for="email"
                            class="form-label"
                        >
                            メールアドレス
                        </label>

                        <input
                            class="form-control"
                            type="email"
                            id="email"
                            name="email"
                            required
                            autocomplete="email"
                        >
                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?= esc_attr((new \HKS\Auth\Token())->getOrGenerate()); ?>"
                        >

                    </div>

                    <div class="form-actions auth-actions">

                        <button
                            type="submit"
                            class="btn btn-primary btn-block"
                        >
                            再設定メールを送信
                        </button>

                    </div>

                </form>

            </div>

            <div class="card-footer auth-links">

                <p>

                    <a href="<?= esc_url(home_url('/login/')); ?>">

                        ログイン画面へ戻る

                    </a>

                </p>

                <p>

                    アカウントをお持ちでない方は

                    <a href="<?= esc_url(home_url('/signup/')); ?>">

                        新規登録はこちら

                    </a>

                </p>

            </div>

        </div>

    </div>

</section>