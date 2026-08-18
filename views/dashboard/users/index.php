<?php

declare(strict_types=1);

defined('ABSPATH') || exit;

$uploadDir = wp_upload_dir();

$uploadBaseUrl = trailingslashit(
    (string) $uploadDir['baseurl']
);

$statusLabels = [
    'temporary' => '仮登録',
    'active'    => '有効',
    'suspended' => '利用停止',
    'deleted'   => '退会',
];

$roleLabels = [
    'super_admin' => '最高管理者',
    'admin'       => '管理者',
    'manager'     => 'マネージャー',
    'staff'       => 'スタッフ',
    'member'      => '会員',
];

?>

<div class="member-management">

    <header class="dashboard-page__header">

        <p class="dashboard-page__eyebrow">
            MEMBER MANAGEMENT
        </p>

        <h1 class="dashboard-page__title">
            会員管理
        </h1>

        <p class="dashboard-page__description">
            登録されている会員情報の確認・管理ができます。
        </p>

    </header>


    <div class="member-list">

        <?php if (empty($users)): ?>

            <div class="member-list-empty">

                <p>
                    登録されている会員はいません。
                </p>

            </div>

        <?php else: ?>

            <div class="member-list__table-wrap">

                <table class="member-list-table">

                    <thead>

                        <tr>
                            <th scope="col">画像</th>
                            <th scope="col">ID</th>
                            <th scope="col">氏名</th>
                            <th scope="col">メールアドレス</th>
                            <th scope="col">権限</th>
                            <th scope="col">ステータス</th>
                            <th scope="col">最終ログイン</th>
                            <th scope="col">操作</th>
                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($users as $member): ?>

                            <?php

                            $memberId = (string) $member['id'];

                            $memberName = trim(
                                (string) $member['last_name']
                                . ' '
                                . (string) $member['first_name']
                            );

                            $email = (string) $member['email'];

                            $role = (string) $member['role'];

                            $status = (string) $member['status'];

                            $roleLabel = $roleLabels[$role] ?? $role;

                            $statusLabel = $statusLabels[$status] ?? $status;

                            $lastLogin = !empty($member['last_login_at'])
                                ? (string) $member['last_login_at']
                                : '-';

                            $detailUrl = add_query_arg(
                                [
                                    'view' => 'user-detail',
                                    'id'   => $member['id'],
                                ],
                                home_url('/dashboard/')
                            );

                            ?>

                            <tr>

                                <!-- Profile Image -->
                                <td class="member-list-table__image">

                                    <?php if (!empty($member['profile_image'])): ?>

                                        <?php

                                        $profileImageUrl = $uploadBaseUrl
                                            . ltrim(
                                                (string) $member['profile_image'],
                                                '/'
                                            );

                                        ?>

                                        <img
                                            src="<?php echo esc_url(
                                                $profileImageUrl
                                            ); ?>"
                                            alt="<?php echo esc_attr(
                                                $memberName
                                            ); ?>"
                                            class="member-list-avatar"
                                            loading="lazy"
                                        >

                                    <?php else: ?>

                                        <div
                                            class="member-list-avatar member-list-avatar--empty"
                                            aria-label="プロフィール画像なし"
                                        >
                                            <span aria-hidden="true">
                                                —
                                            </span>
                                        </div>

                                    <?php endif; ?>

                                </td>


                                <!-- ID -->
                                <td class="member-list-table__id">

                                    <?php echo esc_html($memberId); ?>

                                </td>


                                <!-- Name -->
                                <td class="member-list-table__name">

                                    <strong>
                                        <?php echo esc_html($memberName); ?>
                                    </strong>

                                </td>


                                <!-- Email -->
                                <td class="member-list-table__email">

                                    <a
                                        href="mailto:<?php echo esc_attr(
                                            $email
                                        ); ?>"
                                    >
                                        <?php echo esc_html($email); ?>
                                    </a>

                                </td>


                                <!-- Role -->
                                <td class="member-list-table__role">

                                    <span class="member-role">
                                        <?php echo esc_html($roleLabel); ?>
                                    </span>

                                </td>


                                <!-- Status -->
                                <td class="member-list-table__status">

                                    <span
                                        class="member-status member-status--<?php
                                        echo esc_attr($status);
                                        ?>"
                                    >
                                        <?php echo esc_html($statusLabel); ?>
                                    </span>

                                </td>


                                <!-- Last Login -->
                                <td class="member-list-table__login">

                                    <?php echo esc_html($lastLogin); ?>

                                </td>


                                <!-- Action -->
                                <td class="member-list-table__actions">

                                    <a
                                        href="<?php echo esc_url(
                                            $detailUrl
                                        ); ?>"
                                        class="member-list-detail-link"
                                        aria-label="<?php echo esc_attr(
                                            $memberName . 'の詳細を見る'
                                        ); ?>"
                                    >
                                        詳細
                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        <?php endif; ?>

    </div>

</div>
