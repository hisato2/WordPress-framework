<?php
/**
 * Template Name: Forgot Password
 * Template Post Type: page
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$title = get_the_title();

require get_template_directory() . '/views/auth/forgot-password.php';

get_footer();