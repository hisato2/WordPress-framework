<?php

declare(strict_types=1);

namespace HKS\Mail;

use HKS\Auth\Auth;

/**
 * メール送信共通サービス
 *
 * すべてのメール送信は、このクラスを経由する。
 *
 * DEBUG MODE:
 *   DEBUG_PASS でログインしている場合、
 *   本来の送信先には送信せず DEBUG_MAIL へ送信する。
 *
 * local:
 *   メールを実送信せず、
 *   wp-content/uploads/hks-mails/ にテキストファイルとして保存する。
 *
 * production / staging / development:
 *   WordPress の wp_mail() を使用して送信する。
 */
class MailService
{
    /**
     * メール送信
     *
     * @param string $to      本来の送信先メールアドレス
     * @param string $subject 件名
     * @param string $message 本文
     * @param array  $headers メールヘッダー
     *
     * @return array{
     *     success: bool,
     *     message: string,
     *     path?: string|null,
     *     filename?: string|null
     * }
     */
    public function send(
        string $to,
        string $subject,
        string $message,
        array $headers = []
    ): array {
        /*
        |--------------------------------------------------------------------------
        | DEBUG MODE 判定
        |--------------------------------------------------------------------------
        |
        | DEBUG_PASS でログインしている場合、
        | 本来のメールアドレスには送信しない。
        |
        | 送信先を DEBUG_MAIL に強制的に変更する。
        |
        */

        $originalTo = $to;

        if ($this->isDebug()) {
            if (
                !defined('DEBUG_MAIL') ||
                !is_string(DEBUG_MAIL) ||
                DEBUG_MAIL === ''
            ) {
                return [
                    'success' => false,
                    'message' => 'DEBUG_MAIL が設定されていないため、メール送信を中止しました。',
                    'path' => null,
                    'filename' => null,
                ];
            }

            $debugMail = sanitize_email(DEBUG_MAIL);

            if (
                $debugMail === '' ||
                !is_email($debugMail)
            ) {
                return [
                    'success' => false,
                    'message' => 'DEBUG_MAIL のメールアドレスが正しくありません。',
                    'path' => null,
                    'filename' => null,
                ];
            }

            $to = $debugMail;

            /*
            |--------------------------------------------------------------------------
            | DEBUGメールであることを件名に表示
            |--------------------------------------------------------------------------
            */

            $subject = '[DEBUG] ' . $subject;

            /*
            |--------------------------------------------------------------------------
            | 本来の送信先を本文に記録
            |--------------------------------------------------------------------------
            |
            | DEBUG_MAIL に届いたメールを確認した際に、
            | 本来誰へ送信される予定だったメールなのか分かるようにする。
            |
            */

            $message = implode(
                PHP_EOL,
                [
                    '========================================',
                    'DEBUG MODE',
                    '本来の送信先: ' . $originalTo,
                    '実際の送信先: ' . $to,
                    '========================================',
                    '',
                    $message,
                ]
            );
        }

        /*
        |--------------------------------------------------------------------------
        | LOCAL環境
        |--------------------------------------------------------------------------
        */

        if ($this->isLocal()) {
            return $this->saveToFile(
                $to,
                $subject,
                $message,
                $headers
            );
        }

        /*
        |--------------------------------------------------------------------------
        | 実メール送信
        |--------------------------------------------------------------------------
        */

        return $this->sendByWordPress(
            $to,
            $subject,
            $message,
            $headers
        );
    }

    /**
     * DEBUG MODE 判定
     */
    private function isDebug(): bool
    {
        $auth = new Auth();

        return $auth->isDebug();
    }

    /**
     * ローカル環境か判定
     */
    private function isLocal(): bool
    {
        return wp_get_environment_type() === 'local';
    }

    /**
     * ローカル環境用
     *
     * メールをファイルとして保存する。
     */
    private function saveToFile(
        string $to,
        string $subject,
        string $message,
        array $headers = []
    ): array {
        $uploadDir = wp_upload_dir();

        if (!empty($uploadDir['error'])) {
            return [
                'success' => false,
                'message' => 'メール保存先の取得に失敗しました。',
                'path' => null,
                'filename' => null,
            ];
        }

        $directory = trailingslashit($uploadDir['basedir'])
            . 'hks-mails';

        /**
         * 保存ディレクトリが存在しなければ作成
         */
        if (!is_dir($directory)) {
            $created = wp_mkdir_p($directory);

            if (!$created) {
                return [
                    'success' => false,
                    'message' => 'メール保存ディレクトリの作成に失敗しました。',
                    'path' => null,
                    'filename' => null,
                ];
            }
        }

        if (!is_writable($directory)) {
            return [
                'success' => false,
                'message' => 'メール保存ディレクトリに書き込みできません。',
                'path' => null,
                'filename' => null,
            ];
        }

        /**
         * 同一秒に複数メールが発生しても
         * ファイル名が重複しないようランダム文字列を付与
         */
        try {
            $random = bin2hex(random_bytes(4));
        } catch (\Throwable $e) {
            $random = uniqid();
        }

        $filename = sprintf(
            'mail-%s-%s.txt',
            date('Ymd-His'),
            $random
        );

        $filePath = trailingslashit($directory)
            . $filename;

        /**
         * ブラウザ表示用の相対パス
         *
         * サーバー内部の絶対パスは画面に表示しない。
         */
        $displayPath = sprintf(
            'wp-content/uploads/hks-mails/%s',
            $filename
        );

        $headerText = '';

        if (!empty($headers)) {
            $headerText = implode(
                PHP_EOL,
                array_map(
                    static fn($header): string => (string) $header,
                    $headers
                )
            );
        }

        $content = implode(
            PHP_EOL,
            [
                '========================================',
                'HKS LOCAL MAIL',
                '========================================',
                'Date: ' . date('Y-m-d H:i:s'),
                'To: ' . $to,
                'Subject: ' . $subject,
                '----------------------------------------',
                'Headers:',
                $headerText,
                '----------------------------------------',
                'Message:',
                $message,
                '========================================',
                '',
            ]
        );

        $result = file_put_contents(
            $filePath,
            $content,
            LOCK_EX
        );

        if ($result === false) {
            return [
                'success' => false,
                'message' => 'メールファイルの保存に失敗しました。',
                'path' => null,
                'filename' => null,
            ];
        }

        return [
            'success' => true,
            'message' => sprintf(
                'ローカル環境のため、メールをファイル出力しました。保存先: %s',
                $displayPath
            ),
            'path' => $displayPath,
            'filename' => $filename,
        ];
    }

    /**
     * WordPress wp_mail() によるメール送信
     */
    private function sendByWordPress(
        string $to,
        string $subject,
        string $message,
        array $headers = []
    ): array {
        $sent = wp_mail(
            $to,
            $subject,
            $message,
            $headers
        );

        if (!$sent) {
            return [
                'success' => false,
                'message' => 'メールの送信に失敗しました。',
                'path' => null,
                'filename' => null,
            ];
        }

        return [
            'success' => true,
            'message' => 'メールを送信しました。',
            'path' => null,
            'filename' => null,
        ];
    }
}