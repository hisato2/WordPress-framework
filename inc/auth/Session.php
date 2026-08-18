<?php

declare(strict_types=1);

namespace HKS\Auth;

class Session
{
    public function __construct()
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
    }

    /**
     * セッションへ値を保存
     */
    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    /**
     * セッションから値を取得
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    /**
     * セッションキーの存在確認
     */
    public function has(string $key): bool
    {
        return isset($_SESSION[$key]);
    }

    /**
     * セッションキーを削除
     */
    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /**
     * 全セッションを取得
     */
    public function all(): array
    {
        return $_SESSION;
    }

    /**
     * セッションをすべてクリア
     */
    public function clear(): void
    {
        $_SESSION = [];
    }

    /**
     * 値を取得して削除
     */
    public function pull(string $key, mixed $default = null): mixed
    {
        $value = $this->get($key, $default);
        $this->remove($key);

        return $value;
    }

    /**
     * セッションIDを再生成
     * （Session Fixation Attack 対策）
     */
    public function regenerate(bool $deleteOldSession = true): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        session_regenerate_id($deleteOldSession);
    }

    /**
     * セッションを破棄
     */
    public function destroy(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            $_SESSION = [];
            return;
        }

        $_SESSION = [];

        if ((bool) ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'],
                (bool) $params['secure'],
                (bool) $params['httponly']
            );
        }

        session_destroy();
    }

    /**
     * Flashメッセージを保存
     */
    public function flash(string $key, mixed $value): void
    {
        $_SESSION['_flash'][$key] = $value;
    }

    /**
     * Flashメッセージを取得（取得後に削除）
     */
    public function getFlash(string $key, mixed $default = null): mixed
    {
        if (!isset($_SESSION['_flash'][$key])) {
            return $default;
        }

        $value = $_SESSION['_flash'][$key];

        unset($_SESSION['_flash'][$key]);

        return $value;
    }
}