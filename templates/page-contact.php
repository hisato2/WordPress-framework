<?php
/**
 * Template Name: Contact
 * Template Post Type: page
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$title = get_the_title();

require get_template_directory() . '/views/contact/index.php';

get_footer();