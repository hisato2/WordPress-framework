<?php

declare(strict_types=1);

namespace HKS\Auth;

/**
 * ロール定義クラス
 *
 * システム全体で利用する権限管理
 */
final class Role
{
    /** システム管理者 */
    public const SUPER_ADMIN = 'super_admin';

    /** 管理者 */
    public const ADMIN = 'admin';

    /** マネージャー */
    public const MANAGER = 'manager';

    /** スタッフ */
    public const STAFF = 'staff';

    /** 一般会員 */
    public const MEMBER = 'member';

    /** ゲスト */
    public const GUEST = 'guest';

    /**
     * 全ロール取得
     */
    public static function all(): array
    {
        return [
            self::SUPER_ADMIN,
            self::ADMIN,
            self::MANAGER,
            self::STAFF,
            self::MEMBER,
            self::GUEST,
        ];
    }

    /**
     * ロール名取得
     */
    public static function label(string $role): string
    {
        return match ($role) {

            self::SUPER_ADMIN => 'システム管理者',

            self::ADMIN => '管理者',

            self::MANAGER => 'マネージャー',

            self::STAFF => 'スタッフ',

            self::MEMBER => '会員',

            default => 'ゲスト',
        };
    }
/**
 * 管理機能アクセス可能
 *
 * staff 以上のロールに管理機能へのアクセスを許可する
 */
public static function canAccessAdmin(string $role): bool
{
    return in_array(
        $role,
        [
            self::SUPER_ADMIN,
            self::ADMIN,
            self::MANAGER,
            self::STAFF,
        ],
        true
    );
}

/**
 * スタッフ以上
 */
public static function isStaff(string $role): bool
{
    return in_array(
        $role,
        [
            self::SUPER_ADMIN,
            self::ADMIN,
            self::MANAGER,
            self::STAFF,
        ],
        true
    );
}

/**
 * 会員以上
 */
public static function isMember(string $role): bool
{
    return in_array(
        $role,
        [
            self::SUPER_ADMIN,
            self::ADMIN,
            self::MANAGER,
            self::STAFF,
            self::MEMBER,
        ],
        true
    );
}
    /**
     * ゲスト判定
     */
    public static function isGuest(string $role): bool
    {
        return $role === self::GUEST;
    }
}