<?php

declare(strict_types=1);

namespace HKS\Auth;

use HKS\Repositories\UserRepository;

class RegisterService
{
    /**
     * @var UserRepository
     */
    private UserRepository $users;

    /**
     * @var Validator
     */
    private Validator $validator;

    public function __construct()
    {
        $this->users = new UserRepository();
        $this->validator = new Validator();
    }

    /**
     * 新規登録
     */
    public function register(array $data): array
    {
        /*
        |--------------------------------------------------------------------------
        | バリデーション
        |--------------------------------------------------------------------------
        */
        if (!$this->validator->validateSignup($data)) {
            return [
                'success' => false,
                'message' => $this->validator->first(),
                'errors'  => $this->validator->errors(),
            ];
        }

        /*
        |--------------------------------------------------------------------------
        | 入力値整形
        |--------------------------------------------------------------------------
        */

        $email = strtolower(trim($data['email']));

        $lastName = trim($data['last_name']);

        $firstName = trim($data['first_name']);

        /*
        |--------------------------------------------------------------------------
        | メール重複
        |--------------------------------------------------------------------------
        */

        if ($this->users->existsByEmail($email)) {

            return [
                'success' => false,
                'message' => 'このメールアドレスは既に登録されています。',
            ];

        }

        /*
        |--------------------------------------------------------------------------
        | パスワード
        |--------------------------------------------------------------------------
        */

        $passwordHash = password_hash(
            $data['password'],
            PASSWORD_DEFAULT
        );

        /*
        |--------------------------------------------------------------------------
        | 会員登録
        |--------------------------------------------------------------------------
        */

        $result = $this->users->create([

            'status' => UserStatus::TEMPORARY,

            'role' => Role::MEMBER,

            'email' => $email,

            'password_hash' => $passwordHash,

            'last_name' => $lastName,

            'first_name' => $firstName,

        ]);

        if (!$result) {

            return [

                'success' => false,

                'message' => '会員登録に失敗しました。',

            ];

        }

        /*
        |--------------------------------------------------------------------------
        | 完了
        |--------------------------------------------------------------------------
        */

        return [

            'success' => true,

            'message' => '会員登録が完了しました。',

        ];
    }
}