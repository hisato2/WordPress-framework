<?php
/**
 * Template Name: Events
 * Template Post Type: page
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$title = get_the_title();

require get_template_directory() . '/views/events/index.php';

get_footer();