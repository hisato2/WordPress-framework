<?php

declare(strict_types=1);

namespace HKS\Members;

use HKS\Auth\Auth;
use HKS\Auth\Role;
use HKS\Auth\UserStatus;
use HKS\Repositories\UserRepository;

defined('ABSPATH') || exit;

class MemberService
{
    private UserRepository $users;

    private Auth $auth;

    public function __construct()
    {
        $this->users = new UserRepository();
        $this->auth = new Auth();
    }

    /**
     * Dashboardから会員情報を更新する
     *
     * @param int $userId
     * @param array<string, mixed> $data
     * @return array<string, mixed>
     */
    public function updateProfile(
        int $userId,
        array $data
    ): array {
        /*
        |--------------------------------------------------------------------------
        | 操作ユーザー確認
        |--------------------------------------------------------------------------
        */

        if (!$this->auth->check()) {
            return [
                'success' => false,
                'message' => 'ログインが必要です。',
            ];
        }

        $operator = $this->auth->user();

        if (empty($operator)) {
            return [
                'success' => false,
                'message' => '操作ユーザーを確認できません。',
            ];
        }

        $operatorRole = (string) ($operator['role'] ?? '');

        /*
        |--------------------------------------------------------------------------
        | 更新権限確認
        |--------------------------------------------------------------------------
        */

        if (
            !in_array(
                $operatorRole,
                [
                    Role::SUPER_ADMIN,
                    Role::ADMIN,
                ],
                true
            )
        ) {
            return [
                'success' => false,
                'message' => '会員情報を更新する権限がありません。',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | 対象会員確認
        |--------------------------------------------------------------------------
        */

        if ($userId <= 0) {
            return [
                'success' => false,
                'message' => '会員IDが正しくありません。',
            ];
        }

        $member = $this->users->findById($userId);

        if ($member === null) {
            return [
                'success' => false,
                'message' => '指定された会員が見つかりません。',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | 入力値
        |--------------------------------------------------------------------------
        */

        $lastName = trim(
            (string) ($data['last_name'] ?? '')
        );

        $firstName = trim(
            (string) ($data['first_name'] ?? '')
        );

        $email = strtolower(
            trim((string) ($data['email'] ?? ''))
        );

        $role = (string) ($data['role'] ?? '');

        $status = (string) ($data['status'] ?? '');

        /*
        |--------------------------------------------------------------------------
        | 基本入力チェック
        |--------------------------------------------------------------------------
        */

        if (
            $lastName === ''
            || $firstName === ''
            || $email === ''
        ) {
            return [
                'success' => false,
                'message' => '氏名とメールアドレスを入力してください。',
            ];
        }

        if (!is_email($email)) {
            return [
                'success' => false,
                'message' => 'メールアドレスの形式が正しくありません。',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | メールアドレス重複確認
        |--------------------------------------------------------------------------
        */

        if (
            $this->users->existsByEmailExceptId(
                $email,
                $userId
            )
        ) {
            return [
                'success' => false,
                'message' => 'このメールアドレスは既に使用されています。',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | role確認
        |--------------------------------------------------------------------------
        */

        $allowedRoles = [
            Role::SUPER_ADMIN,
            Role::ADMIN,
            Role::MANAGER,
            Role::STAFF,
            Role::MEMBER,
        ];

        if (!in_array($role, $allowedRoles, true)) {
            return [
                'success' => false,
                'message' => '不正な権限が指定されています。',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | status確認
        |--------------------------------------------------------------------------
        */

        if (!in_array($status, UserStatus::all(), true)) {
            return [
                'success' => false,
                'message' => '不正なステータスが指定されています。',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | super_admin保護
        |--------------------------------------------------------------------------
        */

        $targetRole = (string) ($member['role'] ?? '');

        if (
            $operatorRole === Role::ADMIN
            && $targetRole === Role::SUPER_ADMIN
        ) {
            return [
                'success' => false,
                'message' => 'システム管理者の情報を変更することはできません。',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | adminによるsuper_admin付与禁止
        |--------------------------------------------------------------------------
        */

        if (
            $operatorRole === Role::ADMIN
            && $role === Role::SUPER_ADMIN
        ) {
            return [
                'success' => false,
                'message' => 'システム管理者権限を付与することはできません。',
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | DB更新データ作成
        |--------------------------------------------------------------------------
        */

        $updateData = [
            'last_name'  => $lastName,
            'first_name' => $firstName,
            'email'      => $email,
            'role'       => $role,
            'status'     => $status,
        ];

        /*
        |--------------------------------------------------------------------------
        | プロフィール画像
        |--------------------------------------------------------------------------
        |
        | 新しい画像がアップロードされた場合のみ更新する。
        | 画像が選択されていない場合は既存値を維持する。
        |
        */

        if (
            isset($data['profile_image'])
            && is_string($data['profile_image'])
            && $data['profile_image'] !== ''
        ) {
            $updateData['profile_image'] = $data['profile_image'];
        }

        /*
        |--------------------------------------------------------------------------
        | DB更新
        |--------------------------------------------------------------------------
        */

        $updated = $this->users->update(
            $userId,
            $updateData
        );

        if (!$updated) {
            return [
                'success' => false,
                'message' => '会員情報を更新できませんでした。',
            ];
        }

        return [
            'success' => true,
            'message' => '会員情報を更新しました。',
        ];
    }
}