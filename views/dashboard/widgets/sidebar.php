<?php

declare(strict_types=1);

?>

<aside class="dashboard-sidebar">

    <div class="dashboard-sidebar__inner">

        <div class="dashboard-sidebar__header">

            <span class="dashboard-sidebar__label">
                MANAGEMENT
            </span>

        </div>


        <nav class="dashboard-nav" aria-label="管理メニュー">

            <ul class="dashboard-nav__list">

                <li class="dashboard-nav__item">

                    <a
                        class="dashboard-nav__link is-active"
                        href="<?= esc_url(home_url('/dashboard/')); ?>"
                    >
                        <span class="dashboard-nav__icon" aria-hidden="true">
                            ◫
                        </span>

                        <span>
                            ダッシュボード
                        </span>
                    </a>

                </li>


                <li class="dashboard-nav__item">

                    <a
                        class="dashboard-nav__link"
                        href="<?= esc_url(home_url('/dashboard/?view=users')); ?>"
                    >
                        <span class="dashboard-nav__icon" aria-hidden="true">
                            ♙
                        </span>

                        <span>
                            会員管理
                        </span>
                    </a>

                </li>



                <li class="dashboard-nav__item">

                    <a
                        class="dashboard-nav__link"
                        href="<?= esc_url(home_url('/jobs/')); ?>"
                    >
                        <span class="dashboard-nav__icon" aria-hidden="true">
                            ▣
                        </span>

                        <span>
                            求人管理
                        </span>
                    </a>

                </li>


                <li class="dashboard-nav__item">

                    <a
                        class="dashboard-nav__link"
                        href="<?= esc_url(home_url('/events/')); ?>"
                    >
                        <span class="dashboard-nav__icon" aria-hidden="true">
                            ◇
                        </span>

                        <span>
                            イベント管理
                        </span>
                    </a>

                </li>


                <li class="dashboard-nav__item">

                    <a
                        class="dashboard-nav__link"
                        href="<?= esc_url(home_url('/news/')); ?>"
                    >
                        <span class="dashboard-nav__icon" aria-hidden="true">
                            ▤
                        </span>

                        <span>
                            お知らせ管理
                        </span>
                    </a>

                </li>


                <li class="dashboard-nav__item">

                    <a
                        class="dashboard-nav__link"
                        href="<?= esc_url(home_url('/contact/')); ?>"
                    >
                        <span class="dashboard-nav__icon" aria-hidden="true">
                            ✉
                        </span>

                        <span>
                            お問い合わせ管理
                        </span>
                    </a>

                </li>

            </ul>

        </nav>


        <div class="dashboard-sidebar__footer">

            <a
                class="dashboard-nav__link"
                href="<?= esc_url(home_url('/mypage/')); ?>"
            >
                <span class="dashboard-nav__icon" aria-hidden="true">
                    ○
                </span>

                <span>
                    マイページ
                </span>
            </a>


            <a
                class="dashboard-nav__link"
                href="<?= esc_url(home_url('/logout/')); ?>"
            >
                <span class="dashboard-nav__icon" aria-hidden="true">
                    ←
                </span>

                <span>
                    ログアウト
                </span>
            </a>

        </div>

    </div>

</aside>