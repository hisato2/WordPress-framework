<?php

declare(strict_types=1);

namespace HKS\Auth;

class Token
{
    /**
     * CSRFトークン生成
     */
    public function generate(string $key = '_token'): string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $token = bin2hex(random_bytes(32));

        $_SESSION[$key] = $token;

        return $token;
    }

    /**
     * CSRFトークン取得
     */
    public function get(string $key = '_token'): ?string
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        return $_SESSION[$key] ?? null;
    }


    /**
     * CSRFトークン取得（存在しなければ生成）
     */
    public function getOrGenerate(string $key = '_token'): string
    {
        $token = $this->get($key);

        if ($token !== null) {
            return $token;
        }

        return $this->generate($key);
    }


    /**
     * CSRFトークン検証
     */
    public function verify(?string $token, string $key = '_token'): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (
            empty($token) ||
            empty($_SESSION[$key])
        ) {
            return false;
        }

        return hash_equals($_SESSION[$key], $token);
    }





    /**
     * CSRFトークン削除
     */
    public function destroy(string $key = '_token'): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        unset($_SESSION[$key]);
    }

    /**
     * API用アクセストークン生成
     */
    public function generateAccessToken(int $length = 64): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * パスワードリセットトークン生成
     */
    public function generateResetToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * メール認証トークン生成
     */
    public function generateVerifyToken(): string
    {
        return bin2hex(random_bytes(32));
    }
}