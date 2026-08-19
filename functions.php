<?php

declare(strict_types=1);

/**
 * Hakuhousha Framework
 *
 * WordPress起動時にFrameworkを初期化する。
 *
 * このファイルには、業務処理・HTML・API処理・
 * WordPress Hookの個別登録を書かない。
 *
 * @package HakuhoushaPortfolio
 */


require_once __DIR__ . '/inc/bootstrap.php';

/**
 * デバッグ表示して処理を停止
 *
 * @param mixed ...$values
 * @return never
 */
function stop(...$values): never
{
    echo '<pre style="
        padding:20px;
        margin:20px;
        background:#111;
        color:#00ff88;
        font-size:14px;
        line-height:1.6;
        white-space:pre-wrap;
        word-break:break-word;
        position:relative;
        z-index:999999;
    ">';

    foreach ($values as $value) {
        var_dump($value);
        echo "\n";
    }

    echo '</pre>';

    exit;
}