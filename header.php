<?php

use HKS\Auth\Auth;

$auth = new Auth();

?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>

    <meta charset="<?php bloginfo('charset'); ?>">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php wp_title('|', true, 'right'); ?>
        <?php bloginfo('name'); ?>
    </title>

    <?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>

    <?php wp_body_open(); ?>


    <?php
    /*
    |--------------------------------------------------------------------------
    | DEBUG MODE 表示
    |--------------------------------------------------------------------------
    |
    | DEBUG_PASS を使用してログインしている場合のみ表示する。
    |
    | 通常ユーザーには表示されない。
    |
    */
    ?>
        <?php if ($auth->isDebug()) : ?>

            <div class="hks-debug-bar">

                <strong>
                    DEBUG MODE
                </strong>

                <?php if (
                    defined('DEBUG_MAIL') &&
                    is_string(DEBUG_MAIL) &&
                    DEBUG_MAIL !== ''
                ) : ?>

                    <span class="hks-debug-bar__mail">
                        ｜ メール送信先：
                        <?php echo esc_html(DEBUG_MAIL); ?>
                    </span>

                <?php endif; ?>

            </div>

        <?php endif; ?>




    <header class="site-header">

        <div class="container">

            <!-- Logo -->
            <div class="logo">

                <a href="<?php echo esc_url(home_url('/')); ?>">

                    <?php bloginfo('name'); ?>

                </a>

            </div>

            <!-- Hamburger -->
            <button
                id="mypage-menu-toggle"
                class="mypage-menu-toggle"
                type="button"
                aria-label="メニューを開閉"
                aria-controls="main-nav"
                aria-expanded="false">

                <span class="mypage-menu-toggle__line"></span>
                <span class="mypage-menu-toggle__line"></span>
                <span class="mypage-menu-toggle__line"></span>

            </button>

            <!-- Navigation -->
            <nav id="main-nav" class="main-nav">

                <ul>

                    <li>
                        <a href="<?php echo esc_url(home_url('/')); ?>">
                            ホーム
                        </a>
                    </li>

                    <?php if ($auth->check()) : ?>


                        <li>
                            <a href="<?php echo esc_url(home_url('/mypage/')); ?>">
                                マイページ
                            </a>
                        </li>

                        <li>
                            <a href="<?php echo esc_url(home_url('/logout/')); ?>">
                                ログアウト
                            </a>
                        </li>

                        <?php if ($auth->canAccessAdmin()) : ?>

                            <li>
                                <a href="<?php echo esc_url(home_url('/dashboard/')); ?>">
                                    ダッシュボード
                                </a>
                            </li>

                        <?php endif; ?>


                    <?php else : ?>

                        <li>
                            <a href="<?php echo esc_url(home_url('/login/')); ?>">
                                ログイン
                            </a>
                        </li>

                    <?php endif; ?>


                </ul>

            </nav>

        </div>

    </header>