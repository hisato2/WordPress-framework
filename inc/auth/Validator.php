<?php

declare(strict_types=1);

namespace HKS\Auth;

class Validator
{
    /**
     * エラーメッセージ
     *
     * @var array
     */
    private array $errors = [];

    /**
     * ログインバリデーション
     */
    public function validateLogin(array $data): bool
    {
        $this->errors = [];

        if (empty(trim($data['email'] ?? ''))) {
            $this->errors['email'] = 'メールアドレスを入力してください。';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'メールアドレスの形式が正しくありません。';
        }

        if (empty($data['password'] ?? '')) {
            $this->errors['password'] = 'パスワードを入力してください。';
        }

        return empty($this->errors);
    }


    /**
     * 新規登録バリデーション
     */
    public function validateSignup(array $data): bool
    {
        $this->errors = [];

        if (empty(trim($data['last_name'] ?? ''))) {
            $this->errors['last_name'] = '姓を入力してください。';
        }

        if (empty(trim($data['first_name'] ?? ''))) {
            $this->errors['first_name'] = '名を入力してください。';
        }

        if (empty(trim($data['email'] ?? ''))) {
            $this->errors['email'] = 'メールアドレスを入力してください。';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'メールアドレスの形式が正しくありません。';
        }

        $this->validatePassword(
            (string) ($data['password'] ?? ''),
            (string) ($data['password_confirmation'] ?? '')
        );

        return empty($this->errors);
    }






/**
 * プロフィール更新バリデーション
 *
 * @param array<string, mixed> $data
 */
    public function validateProfile(array $data): bool
    {
        $this->errors = [];


        /*
        |--------------------------------------------------------------------------
        | 姓
        |--------------------------------------------------------------------------
        */

        if (empty(trim((string) ($data['last_name'] ?? '')))) {
            $this->errors['last_name'] = '姓を入力してください。';
        }


        /*
        |--------------------------------------------------------------------------
        | 名
        |--------------------------------------------------------------------------
        */

        if (empty(trim((string) ($data['first_name'] ?? '')))) {
            $this->errors['first_name'] = '名を入力してください。';
        }


        /*
        |--------------------------------------------------------------------------
        | メールアドレス
        |--------------------------------------------------------------------------
        */

        $email = trim((string) ($data['email'] ?? ''));


        if ($email === '') {
            $this->errors['email'] = 'メールアドレスを入力してください。';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'メールアドレスの形式が正しくありません。';
        }


        /*
        |--------------------------------------------------------------------------
        | 生年月日
        |--------------------------------------------------------------------------
        |
        | 生年月日は任意。
        | 入力されている場合のみ YYYY-MM-DD 形式を確認する。
        |
        */

        $birthday = trim((string) ($data['birthday'] ?? ''));


        if ($birthday !== '') {

            $date = \DateTime::createFromFormat(
                'Y-m-d',
                $birthday
            );


            if (
                !$date ||
                $date->format('Y-m-d') !== $birthday
            ) {
                $this->errors['birthday']
                    = '生年月日の形式が正しくありません。';
            }
        }


        return empty($this->errors);
    }



    /**
     * パスワードリセット要求
     */
    public function validateForgotPassword(array $data): bool
    {
        $this->errors = [];

        if (empty(trim($data['email'] ?? ''))) {
            $this->errors['email'] = 'メールアドレスを入力してください。';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $this->errors['email'] = 'メールアドレスの形式が正しくありません。';
        }

        return empty($this->errors);
    }


    /**
     * パスワード再設定
     */
    public function validateResetPassword(array $data): bool
    {
        $this->errors = [];

        if (empty($data['token'] ?? '')) {
            $this->errors['token'] = 'トークンが不正です。';
        }

        $this->validatePassword(
            (string) ($data['password'] ?? ''),
            (string) ($data['password_confirmation'] ?? '')
        );

        return empty($this->errors);
    }



    /**
     * パスワード共通バリデーション
     */
    private function validatePassword(
        string $password,
        string $confirmation
    ): void
    {
        if ($password === '') {
            $this->errors['password'] = 'パスワードを入力してください。';
        } elseif (strlen($password) < 8) {
            $this->errors['password'] = 'パスワードは8文字以上で入力してください。';
        }

        if ($confirmation === '') {
            $this->errors['password_confirmation'] = '確認用パスワードを入力してください。';
        } elseif ($password !== $confirmation) {
            $this->errors['password_confirmation'] = 'パスワードが一致しません。';
        }
    }




    /**
     * 任意データの必須チェック
     */
    public function required(array $data, array $fields): bool
    {
        $this->errors = [];

        foreach ($fields as $field) {
            if (empty(trim($data[$field] ?? ''))) {
                $this->errors[$field] = "{$field}は必須です。";
            }
        }

        return empty($this->errors);
    }

    /**
     * エラー取得
     */
    public function errors(): array
    {
        return $this->errors;
    }

    /**
     * エラー有無
     */
    public function hasErrors(): bool
    {
        return !empty($this->errors);
    }

    /**
     * 最初のエラー取得
     */
    public function first(): ?string
    {
        return empty($this->errors)
            ? null
            : reset($this->errors);
    }
}