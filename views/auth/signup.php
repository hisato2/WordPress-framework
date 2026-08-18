<?php

declare(strict_types=1);

?>

<section class="auth">

    <div class="auth-container">

        <div class="card auth-card">

            <div class="card-header">

                <h1 class="card-title">
                    新規登録
                </h1>

            </div>

            <?php if ($error !== '') : ?>

                <div class="message message--error">

                    <?= esc_html($error); ?>

                </div>

            <?php endif; ?>

            <div class="card-body">

                <form method="post" class="auth-form">

                    <div class="form-group">

                        <label
                            for="last_name"
                            class="form-label">
                            姓
                        </label>

                        <input
                            type="text"
                            id="last_name"
                            name="last_name"
                            class="form-control"
                            required
                            autocomplete="family-name">

                    </div>

                    <div class="form-group">

                        <label
                            for="first_name"
                            class="form-label">
                            名
                        </label>

                        <input
                            type="text"
                            id="first_name"
                            name="first_name"
                            class="form-control"
                            required
                            autocomplete="given-name">

                    </div>

                    <div class="form-group">

                        <label
                            for="email"
                            class="form-label">
                            メールアドレス
                        </label>

                        <input
                            type="email"
                            id="email"
                            name="email"
                            class="form-control"
                            required
                            autocomplete="email">

                    </div>

                    <div class="form-group">

                        <label
                            for="password"
                            class="form-label">
                            パスワード
                        </label>

                        <input
                            type="password"
                            id="password"
                            name="password"
                            class="form-control"
                            required
                            autocomplete="new-password">

                    </div>


                    <div class="form-group">

                        <label
                            for="password_confirmation"
                            class="form-label">
                            パスワード（確認）
                        </label>

                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            class="form-control"
                            required
                            autocomplete="new-password">

                    </div>


                    <div class="form-actions">

                        <button
                            type="submit"
                            class="btn btn-primary">
                            新規登録
                        </button>

                    </div>

                </form>

            </div>

            <div class="card-footer auth-links">

                <p>

                    すでにアカウントをお持ちですか？

                    <a href="<?= esc_url(home_url('/login/')); ?>">
                        ログインはこちら
                    </a>

                </p>

            </div>

        </div>

    </div>

</section>