<?php

declare(strict_types=1);

?>

<section class="dashboard">

    <div class="dashboard-layout">

        <?php
        require get_template_directory()
            . '/views/dashboard/widgets/sidebar.php';
        ?>

        <main class="dashboard-content">

            <?php require $contentView; ?>

        </main>

    </div>

</section>