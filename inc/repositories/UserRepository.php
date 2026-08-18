<?php

declare(strict_types=1);

namespace HKS\Repositories;

if (!defined('ABSPATH')) {
    exit;
}

class UserRepository
{
    /**
     * WordPress Database
     */
    private \wpdb $wpdb;

    /**
     * テーブル名
     */
    private string $table = 'hks_users';

    public function __construct()
    {
        global $wpdb;

        $this->wpdb = $wpdb;
    }

    /**
     * メールアドレス重複チェック
     */
    public function existsByEmail(string $email): bool
    {
        $sql = $this->wpdb->prepare(
            "
            SELECT COUNT(*)
            FROM {$this->table}
            WHERE email = %s
            ",
            $email
        );

        return (int) $this->wpdb->get_var($sql) > 0;
    }



    /**
     * 指定した会員IDを除外して
     * メールアドレスの重複を確認
     *
     * 会員情報編集時に使用する。
     */
    public function existsByEmailExceptId(
        string $email,
        int $excludeId
    ): bool {

        $sql = $this->wpdb->prepare(
            "
            SELECT COUNT(*)
            FROM {$this->table}
            WHERE email = %s
            AND id <> %d
            ",
            $email,
            $excludeId
        );

        return (int) $this->wpdb->get_var($sql) > 0;
    }






    /**
     * 会員登録
     */
    public function create(array $user): bool
    {
        $result = $this->wpdb->insert(
            $this->table,
            [
                'email'         => $user['email'],
                'password_hash' => $user['password_hash'],
                'last_name'     => $user['last_name'],
                'first_name'    => $user['first_name'],
                'status'        => $user['status'],
                'role'          => $user['role'],
                'created_at'    => current_time('mysql'),
                'updated_at'    => current_time('mysql'),
            ],
            [
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
                '%s',
            ]
        );

        return $result !== false;
    }

    /**
     * メールアドレスから会員取得
     */
    public function findByEmail(string $email): ?array
    {
        $sql = $this->wpdb->prepare(
            "
            SELECT *
            FROM {$this->table}
            WHERE email = %s
            LIMIT 1
            ",
            $email
        );

        $user = $this->wpdb->get_row($sql, ARRAY_A);

        return $user ?: null;
    }

    /**
     * IDから会員取得
     */
    public function findById(int $id): ?array
    {
        $sql = $this->wpdb->prepare(
            "
            SELECT *
            FROM {$this->table}
            WHERE id = %d
            LIMIT 1
            ",
            $id
        );

        $user = $this->wpdb->get_row($sql, ARRAY_A);

        return $user ?: null;
    }


    /**
     * メール認証トークン保存
     */
    public function saveVerifyToken(
        int $id,
        string $token,
        string $expires
    ): bool {

        return $this->update($id, [
            'verify_token'      => $token,
            'verify_expires_at' => $expires,
        ]);
    }

    /**
     * メール認証トークンで会員取得
     */
    public function findByVerifyToken(
        string $token
    ): ?array {

        $sql = $this->wpdb->prepare(
            "
            SELECT *
            FROM {$this->table}
            WHERE verify_token = %s
            LIMIT 1
            ",
            $token
        );

        $user = $this->wpdb->get_row(
            $sql,
            ARRAY_A
        );

        return $user ?: null;
    }



    /**
     * 最終ログイン日時更新
     */
    public function updateLastLogin(int $id): bool
    {
        return $this->update($id, [
            'last_login_at' => current_time('mysql')
        ]);
    }

    /**
     * パスワードリセットトークン保存
     */
    public function saveResetToken(
        int $id,
        string $token,
        string $expires
    ): bool {

        return $this->update($id, [
            'reset_token'      => $token,
            'reset_expires_at' => $expires
        ]);
    }



    /**
     * プロフィール情報更新
     *
     * プロフィール画面から変更可能な項目だけを更新する。
     */
    public function updateProfile(
        int $id,
        array $data
    ): bool {

        $allowedFields = [
            'last_name',
            'first_name',
            'last_name_kana',
            'first_name_kana',
            'phone',
            'postal_code',
            'prefecture',
            'city',
            'address1',
            'address2',
            'birthday',
        ];


        $updateData = [];


        foreach ($allowedFields as $field) {

            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }


        if ($updateData === []) {
            return false;
        }


        return $this->update(
            $id,
            $updateData
        );
    }



    /**
     * 会員情報更新
     */
    public function update(
        int $id,
        array $data
    ): bool {

        $data['updated_at'] = current_time('mysql');

        $result = $this->wpdb->update(
            $this->table,
            $data,
            [
                'id' => $id
            ]
        );

        return $result !== false;
    }

    /**
     * パスワード更新
     */
    public function updatePassword(
        int $id,
        string $passwordHash
    ): bool {

        return $this->update($id, [
            'password_hash' => $passwordHash
        ]);
    }

    /**
     * リセットトークンで会員取得
     */
    public function findByResetToken(
        string $token
    ): ?array {

        $sql = $this->wpdb->prepare(
            "
            SELECT *
            FROM {$this->table}
            WHERE reset_token = %s
            LIMIT 1
            ",
            $token
        );

        $user = $this->wpdb->get_row($sql, ARRAY_A);

        return $user ?: null;
    }

    /**
     * リセットトークン削除
     */
    public function clearResetToken(
        int $id
    ): bool {

        return $this->update($id, [
            'reset_token'      => null,
            'reset_expires_at' => null,
        ]);
    }

    /**
     * 会員削除
     */
    public function delete(int $id): bool
    {
        $result = $this->wpdb->delete(
            $this->table,
            [
                'id' => $id
            ],
            [
                '%d'
            ]
        );

        return $result !== false;
    }

    /**
     * 会員一覧取得
     */
    public function all(): array
    {
        return $this->wpdb->get_results(
            "
            SELECT *
            FROM {$this->table}
            ORDER BY id DESC
            ",
            ARRAY_A
        );
    }

    /**
     * プロフィール画像パス更新
     */
    public function updateProfileImage(
        int $id,
        string $imagePath
    ): bool {

        return $this->update(
            $id,
            [
                'profile_image' => $imagePath,
            ]
        );
    }


}