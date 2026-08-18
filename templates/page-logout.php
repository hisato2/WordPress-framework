<?php

declare(strict_types=1);

/**
 * Template Name: Logout
 * Template Post Type: page
 */

use HKS\Auth\Auth;

$auth = new Auth();

$auth->logout();

wp_safe_redirect(home_url('/login/?logout=1'));
exit;