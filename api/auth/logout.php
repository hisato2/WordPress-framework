<?php

declare(strict_types=1);

use HKS\Auth\Auth;

if (!defined('ABSPATH')) {
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    wp_die(
        '不正なアクセスです。',
        'アクセスエラー',
        ['response' => 405]
    );
}


$auth = new Auth();
$auth->logout();

wp_safe_redirect(home_url('/login/?logout=1'));
exit;