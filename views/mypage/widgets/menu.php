<div class="card">

    <h2>メニュー</h2>

    <ul class="mypage-menu">

        <li>
            <a href="<?= esc_url(home_url('/mypage/')); ?>">
                ダッシュボード
            </a>
        </li>

        <li>
            <a href="<?= esc_url(home_url('/profile/')); ?>">
                プロフィール
            </a>
        </li>

        <li>
            <a href="<?= esc_url(home_url('/jobs/')); ?>">
                求人一覧
            </a>
        </li>

        <li>
            <a href="<?= esc_url(home_url('/events/')); ?>">
                イベント
            </a>
        </li>

        <li>
            <a href="<?= esc_url(home_url('/contact/')); ?>">
                お問い合わせ
            </a>
        </li>

        <li>
            <a href="<?= esc_url(home_url('/logout/')); ?>">
                ログアウト
            </a>
        </li>

    </ul>

</div>