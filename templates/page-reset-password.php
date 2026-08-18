<?php

declare(strict_types=1);

/**
 * Template Name: Reset Password
 * Template Post Type: page
 */

/*
|--------------------------------------------------------------------------
| 初期値
|--------------------------------------------------------------------------
*/

$title = get_the_title();

/*
|--------------------------------------------------------------------------
| View
|--------------------------------------------------------------------------
*/

get_header();

require get_template_directory() . '/views/auth/reset-password.php';

get_footer();