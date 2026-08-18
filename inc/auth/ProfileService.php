<?php

declare(strict_types=1);

namespace HKS\Auth;

use HKS\Repositories\UserRepository;

class ProfileService
{
    private UserRepository $users;

    private Validator $validator;

    private Session $session;

    public function __construct()
    {
        $this->users = new UserRepository();
        $this->validator = new Validator();
        $this->session = new Session();
    }

    /**
     * プロフィール更新
     *
     * @param array<string,mixed> $data
     * @return array<string,mixed>
     */
    public function update(array $data): array
    {
        $user = $this->session->get('user');

        if (!$user || empty($user['id'])) {
            return [
                'success' => false,
                'message' => 'ログイン情報がありません。',
            ];
        }

        if (!$this->validator->validateProfile($data)) {
            return [
                'success' => false,
                'message' => $this->validator->first(),
                'errors'  => $this->validator->errors(),
            ];
        }

        $id = (int)$user['id'];

        $current = $this->users->findById($id);

        if (!$current) {
            return [
                'success' => false,
                'message' => '会員情報が見つかりません。',
            ];
        }

        $email = strtolower(trim((string)$data['email']));

        if (
            $email !== (string)$current['email']
            && $this->users->existsByEmail($email)
        ) {
            return [
                'success' => false,
                'message' => 'このメールアドレスは既に使用されています。',
            ];
        }

        $update = [

            'email'            => $email,

            'last_name'        => trim((string)$data['last_name']),

            'first_name'       => trim((string)$data['first_name']),

            'last_name_kana'   => trim((string)$data['last_name_kana']),

            'first_name_kana'  => trim((string)$data['first_name_kana']),

            'phone'            => trim((string)$data['phone']),

            'postal_code'      => trim((string)$data['postal_code']),

            'prefecture'       => trim((string)$data['prefecture']),

            'city'             => trim((string)$data['city']),

            'address1'         => trim((string)$data['address1']),

            'address2'         => trim((string)$data['address2']),

            'birthday'         => trim((string)($data['birthday'] ?? '')) !== ''
                ? trim((string)$data['birthday'])
                : null,
            ];


        if (!$this->users->update($id, $update)) {
            return [
                'success' => false,
                'message' => 'プロフィールを更新できませんでした。',
            ];
        }

        $user = $this->users->findById($id);

        if ($user) {

            unset(
                $user['password_hash'],
                $user['remember_token'],
                $user['reset_token'],
                $user['reset_expires_at']
            );

            $this->session->set('user', $user);
        }

        return [
            'success' => true,
            'message' => 'プロフィールを更新しました。',
            'user'    => $user,
        ];
    }

/**
 * プロフィール画像更新
 *
 * @param string $imagePath
 * @return array<string, mixed>
 */
    public function updateProfileImage(
        string $imagePath
    ): array {

        /*
        |--------------------------------------------------------------------------
        | ログインユーザー取得
        |--------------------------------------------------------------------------
        */

        $user = $this->session->get('user');


        if (
            !$user
            || empty($user['id'])
        ) {
            return [
                'success' => false,
                'message' => 'ログイン情報がありません。',
            ];
        }


        $id = (int) $user['id'];


        /*
        |--------------------------------------------------------------------------
        | 画像パス確認
        |--------------------------------------------------------------------------
        */

        $imagePath = trim($imagePath);


        if ($imagePath === '') {
            return [
                'success' => false,
                'message' => 'プロフィール画像のパスが正しくありません。',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | DB更新
        |--------------------------------------------------------------------------
        */

        if (
            !$this->users->updateProfileImage(
                $id,
                $imagePath
            )
        ) {
            return [
                'success' => false,
                'message' => 'プロフィール画像を更新できませんでした。',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | 最新ユーザー情報取得
        |--------------------------------------------------------------------------
        */

        $user = $this->users->findById($id);


        if (!$user) {
            return [
                'success' => false,
                'message' => '更新後の会員情報を取得できませんでした。',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | セッションに不要な情報を削除
        |--------------------------------------------------------------------------
        */

        unset(
            $user['password_hash'],
            $user['remember_token'],
            $user['reset_token'],
            $user['reset_expires_at']
        );


        /*
        |--------------------------------------------------------------------------
        | セッション更新
        |--------------------------------------------------------------------------
        */

        $this->session->set(
            'user',
            $user
        );


        /*
        |--------------------------------------------------------------------------
        | 更新成功
        |--------------------------------------------------------------------------
        */

        return [
            'success' => true,
            'message' => 'プロフィール画像を更新しました。',
            'user'    => $user,
        ];
    }

}