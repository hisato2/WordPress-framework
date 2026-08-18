<?php

declare(strict_types=1);
?>

<section class="auth">

    <div class="auth-container">

        <div class="card auth-card">

            <div class="card-header auth-header">

                <h1 class="card-title auth-title">
                    ログイン
                </h1>

            </div>

            <div class="card-body">

                <?php if ($error !== '') : ?>

                    <div class="message message--error">

                        <?= esc_html($error); ?>

                    </div>

                <?php endif; ?>

                <form method="post" class="auth-form">

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

                    </div>

                    <div class="form-group">

                        <label
                            for="password"
                            class="form-label"
                        >
                            パスワード
                        </label>

                        <input
                            class="form-control"
                            type="password"
                            id="password"
                            name="password"
                            required
                            autocomplete="current-password"
                        >

                    </div>

                    <div class="form-actions auth-actions">

                        <button
                            type="submit"
                            class="btn btn-primary btn-block"
                        >
                            ログイン
                        </button>

                    </div>

                </form>

            </div>

            <div class="card-footer auth-links">

                <p>

                    <a href="<?= esc_url(home_url('/forgot-password/')); ?>">

                        パスワードをお忘れですか？

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