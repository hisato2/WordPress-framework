<?php

declare(strict_types=1);


/**
 * Hakuhousha Framework Route Definitions
 *
 * action:
 * WordPress admin-post.phpへ送信するaction名
 *
 * file:
 * api/からの相対パス
 *
 * public:
 * trueの場合は未ログイン状態でも実行可能
 */


return [

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    */

    [
        'action' => 'hks_login',
        'file'   => 'auth/login.php',
        'public' => true,
    ],

    [
        'action' => 'hks_logout',
        'file'   => 'auth/logout.php',
        'public' => false,
    ],

    [
        'action' => 'hks_signup',
        'file'   => 'auth/signup.php',
        'public' => true,
    ],

    [
        'action' => 'hks_forgot_password',
        'file'   => 'auth/forgot-password.php',
        'public' => true,
    ],

    [
        'action' => 'hks_reset_password',
        'file'   => 'auth/reset-password.php',
        'public' => true,
    ],

    [
        'action' => 'hks_refresh',
        'file'   => 'auth/refresh.php',
        'public' => false,
    ],


    /*
    |--------------------------------------------------------------------------
    | Profile
    |--------------------------------------------------------------------------
    */

    [
        'action' => 'hks_change_password',
        'file'   => 'profile/change-password.php',
        'public' => false,
    ],


    /*
    |--------------------------------------------------------------------------
    | Member Management
    |--------------------------------------------------------------------------
    */

    [
        'action' => 'hks_update_member_profile',
        'file'   => 'members/update-profile.php',
        'public' => false,
    ],

    [
        'action' => 'hks_admin_change_member_password',
        'file'   => 'members/change-password.php',
        'public' => false,
    ],

];