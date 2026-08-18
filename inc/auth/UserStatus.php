<?php

declare(strict_types=1);

namespace HKS\Auth;

/**
 * 会員ステータス定義
 *
 * hks_users.status と一致させる。
 */
final class UserStatus
{
    /** 仮登録 */
    public const TEMPORARY = 'temporary';

    /** 有効 */
    public const ACTIVE = 'active';

    /** 利用停止 */
    public const SUSPENDED = 'suspended';

    /** 退会（論理削除） */
    public const DELETED = 'deleted';

    /**
     * 利用可能な会員ステータスを取得
     *
     * @return array<int, string>
     */
    public static function all(): array
    {
        return [
            self::TEMPORARY,
            self::ACTIVE,
            self::SUSPENDED,
            self::DELETED,
        ];
    }
}