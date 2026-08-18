<div class="card mt-40">

    <h2>アカウント</h2>

    <table>
        <tbody>
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

</div>