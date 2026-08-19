<?php
declare(strict_types=1);

get_header();


if (have_posts()) {

    while (have_posts()) {

        the_post();

        the_content();
    }

} else {

    require get_template_directory() . '/views/errors/404.php';
}

get_footer();