<div class="card mt-40">

    <h2>プロフィール</h2>

    <table>
        <tbody>
            <tr>
                <th>氏名</th>
                <td><?= esc_html($user['last_name'] . ' ' . $user['first_name']) ?></td>
            </tr>
            <tr>
                <th>メールアドレス</th>
                <td><?= esc_html($user['email']) ?></td>
            </tr>
            <tr>
                <th>ステータス</th>
                <td><?= esc_html($user['status']) ?></td>
            </tr>
            <tr>
                <th>権限</th>
                <td><?= esc_html($user['role']) ?></td>
            </tr>
        </tbody>
    </table>

    <p class="mt-20">
        <a href="<?= esc_url(home_url('/profile/')); ?>">
            プロフィールを編集
        </a>
    </p>

</div>