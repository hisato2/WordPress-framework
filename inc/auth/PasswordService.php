<?php

declare(strict_types=1);

namespace HKS\Auth;

use HKS\Mail\MailService;
use HKS\Repositories\UserRepository;

class PasswordService
{
    /**
     * @var UserRepository
     */
    private UserRepository $users;

    /**
     * @var Validator
     */
    private Validator $validator;

    /**
     * @var Token
     */
    private Token $token;

    /**
     * @var MailService
     */
    private MailService $mail;

    public function __construct()
    {
        $this->users = new UserRepository();
        $this->validator = new Validator();
        $this->token = new Token();
        $this->mail = new MailService();
    }

    /**
     * パスワード再設定メール送信
     */
    public function forgotPassword(array $data): array
    {
        if (!$this->validator->validateForgotPassword($data)) {
            return [
                'success' => false,
                'message' => $this->validator->first(),
                'errors'  => $this->validator->errors(),
            ];
        }

        $email = strtolower(
            trim((string)$data['email'])
        );

        $user = $this->users->findByEmail($email);

        if (!$user) {
            return [
                'success' => false,
                'message' => 'メールアドレスが登録されていません。',
            ];
        }

        /**
         * パスワード再設定トークン生成
         */
        $resetToken = $this->token->generateResetToken();

        /**
         * 有効期限：24時間
         */
        $expires = date(
            'Y-m-d H:i:s',
            strtotime('+1 day')
        );

        /**
         * DBへ再設定トークンを保存
         */
        $this->users->saveResetToken(
            (int)$user['id'],
            $resetToken,
            $expires
        );

        /**
         * パスワード再設定URL
         */
        $resetUrl =
            home_url('/reset-password') .
            '?token=' .
            urlencode($resetToken);

        /**
         * メール内容
         */
        $subject = 'パスワード再設定';

        $message = <<<TEXT
パスワード再設定のご依頼を受け付けました。

下記URLより24時間以内に再設定してください。

{$resetUrl}

このメールに心当たりがない場合は破棄してください。
TEXT;

        /**
         * メール送信
         *
         * local:
         *   ファイルとして保存
         *
         * production:
         *   wp_mail() で送信
         */
        $mailResult = $this->mail->send(
            $email,
            $subject,
            $message
        );

        if (!$mailResult['success']) {
            return [
                'success' => false,
                'message' => $mailResult['message'],
            ];
        }

        return [
            'success'   => true,
            'message'   => $mailResult['message'],
            'mail_file' => $mailResult['path'] ?? null,
        ];
    }

    /**
     * パスワード再設定
     */
    public function resetPassword(array $data): array
    {
        if (!$this->validator->validateResetPassword($data)) {
            return [
                'success' => false,
                'message' => $this->validator->first(),
                'errors'  => $this->validator->errors(),
            ];
        }

        $user = $this->users->findByResetToken(
            (string)$data['token']
        );

        if (!$user) {
            return [
                'success' => false,
                'message' => 'トークンが無効です。',
            ];
        }

        $result = $this->users->updatePassword(
            (int)$user['id'],
            password_hash(
                $data['password'],
                PASSWORD_DEFAULT
            )
        );

        if (!$result) {
            return [
                'success' => false,
                'message' => 'パスワードを更新できませんでした。',
            ];
        }

        /**
         * 使用済みトークンを削除
         */
        $this->users->clearResetToken(
            (int)$user['id']
        );

        return [
            'success' => true,
            'message' => 'パスワードを変更しました。',
        ];
    }



    /**
     * ログイン中ユーザー本人のパスワード変更
     */
    public function changePassword(
        int $userId,
        array $data
    ): array {

        $currentPassword = (string) (
            $data['current_password'] ?? ''
        );


        $newPassword = (string) (
            $data['new_password'] ?? ''
        );


        $newPasswordConfirmation = (string) (
            $data['new_password_confirmation'] ?? ''
        );


        /*
        |--------------------------------------------------------------------------
        | 入力チェック
        |--------------------------------------------------------------------------
        */

        if (
            $currentPassword === ''
            || $newPassword === ''
            || $newPasswordConfirmation === ''
        ) {
            return [
                'success' => false,
                'message' => 'すべての項目を入力してください。',
            ];
        }


        if (strlen($newPassword) < 8) {
            return [
                'success' => false,
                'message' => '新しいパスワードは8文字以上で入力してください。',
            ];
        }


        if ($newPassword !== $newPasswordConfirmation) {
            return [
                'success' => false,
                'message' => '新しいパスワードが確認用と一致しません。',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | ユーザー取得
        |--------------------------------------------------------------------------
        */

        $user = $this->users->findById($userId);


        if (
            !$user
            || empty($user['password_hash'])
        ) {
            return [
                'success' => false,
                'message' => 'ユーザー情報を取得できませんでした。',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | 現在のパスワード確認
        |--------------------------------------------------------------------------
        */

        if (
            !password_verify(
                $currentPassword,
                (string) $user['password_hash']
            )
        ) {
            return [
                'success' => false,
                'message' => '現在のパスワードが正しくありません。',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | 同一パスワード防止
        |--------------------------------------------------------------------------
        */

        if (
            password_verify(
                $newPassword,
                (string) $user['password_hash']
            )
        ) {
            return [
                'success' => false,
                'message' => '現在とは異なるパスワードを設定してください。',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | 新しいパスワードを保存
        |--------------------------------------------------------------------------
        */

        $passwordHash = password_hash(
            $newPassword,
            PASSWORD_DEFAULT
        );


        $updated = $this->users->updatePassword(
            $userId,
            $passwordHash
        );


        if (!$updated) {
            return [
                'success' => false,
                'message' => 'パスワードを変更できませんでした。',
            ];
        }


        return [
            'success' => true,
            'message' => 'パスワードを変更しました。',
        ];







    }


    /**
     * 管理者による会員パスワード変更
     */
    public function changeMemberPassword(
        int $userId,
        array $data
    ): array {

        $newPassword = (string) (
            $data['new_password'] ?? ''
        );


        $newPasswordConfirmation = (string) (
            $data['new_password_confirmation'] ?? ''
        );


        /*
        |--------------------------------------------------------------------------
        | 対象会員ID確認
        |--------------------------------------------------------------------------
        */

        if ($userId <= 0) {
            return [
                'success' => false,
                'message' => '対象会員を確認できませんでした。',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | 入力チェック
        |--------------------------------------------------------------------------
        */

        if (
            $newPassword === ''
            || $newPasswordConfirmation === ''
        ) {
            return [
                'success' => false,
                'message' => 'すべての項目を入力してください。',
            ];
        }


        if (strlen($newPassword) < 8) {
            return [
                'success' => false,
                'message' => '新しいパスワードは8文字以上で入力してください。',
            ];
        }


        if ($newPassword !== $newPasswordConfirmation) {
            return [
                'success' => false,
                'message' => '新しいパスワードが確認用と一致しません。',
            ];
        }


   

        /*
        |--------------------------------------------------------------------------
        | 対象会員取得
        |--------------------------------------------------------------------------
        */

        $user = $this->users->findById($userId);


        if (!$user) {
            return [
                'success' => false,
                'message' => '対象会員が見つかりませんでした。',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | 同一パスワード防止
        |--------------------------------------------------------------------------
        */

        $currentPasswordHash = isset($user['password_hash'])
            ? (string) $user['password_hash']
            : '';


        if (
            $currentPasswordHash !== ''
            && password_verify(
                $newPassword,
                $currentPasswordHash
            )
        ) {
            return [
                'success' => false,
                'message' => '現在とは異なるパスワードを設定してください。',
            ];
        }


        /*
        |--------------------------------------------------------------------------
        | 新しいパスワードを保存
        |--------------------------------------------------------------------------
        */

        $passwordHash = password_hash(
            $newPassword,
            PASSWORD_DEFAULT
        );


        $updated = $this->users->updatePassword(
            $userId,
            $passwordHash
        );


        if (!$updated) {
            return [
                'success' => false,
                'message' => 'パスワードを変更できませんでした。',
            ];
        }


        return [
            'success' => true,
            'message' => '会員のパスワードを変更しました。',
        ];
    }


}