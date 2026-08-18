<?php

declare(strict_types=1);

$auth = new \HKS\Auth\Auth();

?>

<footer class="site-footer">

    <div class="container">

        <div class="footer-grid">

            <!-- サイト情報 -->
            <div class="footer-column">

                <h3 class="footer-title">
                    <?php bloginfo('name'); ?>
                </h3>

                <p>
                    WordPress × カスタムPHP × 独立データベース<br>
                    大学・学会・中小企業様のDXを支援します。
                </p>

            </div>


            <!-- ナビゲーション -->
            <div class="footer-column">

                <h3 class="footer-title">
                    Menu
                </h3>

                <ul class="footer-menu">

                    <li>
                        <a href="<?= esc_url(home_url('/')); ?>">
                            ホーム
                        </a>
                    </li>

                    <li>
                        <a href="<?= esc_url(home_url('/news/')); ?>">
                            お知らせ
                        </a>
                    </li>

                    <li>
                        <a href="<?= esc_url(home_url('/events/')); ?>">
                            イベント
                        </a>
                    </li>

                    <li>
                        <a href="<?= esc_url(home_url('/jobs/')); ?>">
                            求人情報
                        </a>
                    </li>

                    <li>
                        <a href="<?= esc_url(home_url('/contact/')); ?>">
                            お問い合わせ
                        </a>
                    </li>


                    <?php if ($auth->check()) : ?>

                        <li>
                            <a href="<?= esc_url(home_url('/profile/')); ?>">
                                プロフィール
                            </a>
                        </li>

                    <?php endif; ?>

                    <li>
                        <a href="<?= esc_url(home_url('/privacy-policy/')); ?>">
                            プライバシーポリシー
                        </a>
                    </li>

                </ul>

            </div>

        </div>


        <div class="footer-bottom">

            <p>

                &copy; <?= date('Y'); ?>

                <?php bloginfo('name'); ?>

                All Rights Reserved.

            </p>

        </div>

    </div>

</footer>

<?php wp_footer(); ?>

</body>

</html>