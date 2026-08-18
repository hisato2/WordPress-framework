<?php

declare(strict_types=1);

namespace HKS\Auth;

class Auth
{
    private LoginService $loginService;

    private RegisterService $registerService;

    private PasswordService $passwordService;

    private ProfileService $profileService;

    private Session $session;

    private Token $token;

    public function __construct()
    {
        $this->loginService = new LoginService();

        $this->registerService = new RegisterService();

        $this->passwordService = new PasswordService();

        $this->profileService = new ProfileService();

        $this->session = new Session();

        $this->token = new Token();
    }

    /**
     * ログイン
     */
    public function login(array $data): array
    {
        return $this->loginService->login($data);
    }

    /**
     * ログアウト
     */
    public function logout(): void
    {
        $this->loginService->logout();
    }

    /**
     * 新規登録
     */
    public function register(array $data): array
    {
        return $this->registerService->register($data);
    }

    /**
     * パスワード再発行メール送信
     */
    public function forgotPassword(array $data): array
    {
        return $this->passwordService->forgotPassword($data);
    }

    /**
     * パスワード再設定
     */
    public function resetPassword(array $data): array
    {
        return $this->passwordService->resetPassword($data);
    }



    /**
     * ログイン中ユーザー本人のパスワード変更
     */
    public function changePassword(array $data): array
    {
        $userId = $this->id();

        if ($userId === null) {
            return [
                'success' => false,
                'message' => 'ログイン情報を確認できませんでした。',
            ];
        }

        return $this->passwordService->changePassword(
            $userId,
            $data
        );
    }




/**
 * 管理者による会員パスワード変更
 */
public function changeMemberPassword(
    int $userId,
    array $data
): array {

    /*
    |--------------------------------------------------------------------------
    | 管理権限確認
    |--------------------------------------------------------------------------
    |
    | API側でも確認するが、
    | Auth層でも管理者以外からの実行を拒否する。
    |
    */

    if (!$this->canAccessAdmin()) {
        return [
            'success' => false,
            'message' => 'この操作を実行する権限がありません。',
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
            'message' => '対象会員を確認できませんでした。',
        ];
    }


    /*
    |--------------------------------------------------------------------------
    | PasswordServiceへ委譲
    |--------------------------------------------------------------------------
    */

    return $this->passwordService->changeMemberPassword(
        $userId,
        $data
    );
}



    /**
     * プロフィール更新
     */
    public function updateProfile(array $data): array
    {
        return $this->profileService->update($data);
    }

    /**
     * ログイン確認
     */
    public function check(): bool
    {
        return $this->session->has('user');
    }

    /**
     * ゲスト判定
     */
    public function guest(): bool
    {
        return !$this->check();
    }


    /**
     * DEBUGモード判定
     */
    public function isDebug(): bool
    {
        return $this->session->get('debug_mode', false) === true;
    }

    
    /**
     * ログインユーザー取得
     */
    public function user(): ?array
    {
        return $this->session->get('user');
    }

    /**
     * ログインユーザーID取得
     */
    public function id(): ?int
    {
        $user = $this->user();

        return isset($user['id'])
            ? (int) $user['id']
            : null;
    }

    /**
     * ロール取得
     */
    public function role(): string
    {
        $user = $this->user();

        return $user['role'] ?? Role::GUEST;
    }

    /**
     * ロール表示名取得
     */
    public function roleLabel(): string
    {
        return Role::label($this->role());
    }

    /**
     * システム管理者
     */
    public function isSuperAdmin(): bool
    {
        return $this->role() === Role::SUPER_ADMIN;
    }

    /**
     * 管理者
     */
    public function isAdmin(): bool
    {
        return $this->role() === Role::ADMIN;
    }

    /**
     * 管理画面アクセス可能
     */
    public function canAccessAdmin(): bool
    {
        return Role::canAccessAdmin($this->role());
    }

    /**
     * スタッフ以上
     */
    public function isStaff(): bool
    {
        return Role::isStaff($this->role());
    }

    /**
     * 会員以上
     */
    public function isMember(): bool
    {
        return Role::isMember($this->role());
    }

    /**
     * ログイン必須
     */
    public function requireLogin(): void
    {
        if ($this->guest()) {
            wp_safe_redirect(home_url('/login/'));
            exit;
        }
    }

    /**
     * Session取得
     */
    public function session(): Session
    {
        return $this->session;
    }

    /**
     * Token取得
     */
    public function token(): Token
    {
        return $this->token;
    }
}