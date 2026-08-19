<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

?>

<main class="site-main">

    <section class="error-404">

        <h1>
            ページが見つかりません
        </h1>

        <p>
            お探しのページは存在しないか、
            移動または削除された可能性があります。
        </p>

        <a href="<?php echo esc_url(home_url('/')); ?>">
            トップページへ戻る
        </a>

    </section>

</main>