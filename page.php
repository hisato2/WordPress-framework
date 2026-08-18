<?php
declare(strict_types=1);

get_header();


$template = get_template_directory() . '/templates/page-' . get_post_field('post_name') . '.php';

if (file_exists($template)) {
    require $template;
    return;
}

status_header(404);

require get_template_directory() . '/404.php';