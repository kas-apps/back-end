<?php
/**
 * タスク管理システム - データベース接続設定
 *
 * セキュリティのベストプラクティス:
 * - 本番環境では、このファイルを.gitignoreに追加
 * - 環境変数から設定を読み込むことを推奨
 * - エラー表示は開発環境のみ
 */

// エラー報告設定（本番環境では無効化すること）
error_reporting(E_ALL);
ini_set('display_errors', 1);

// データベース接続設定
define('DB_HOST', 'localhost');
define('DB_PORT', '8889'); // MAMPのデフォルトポート
define('DB_NAME', 'task_manager');
define('DB_USER', 'root');
define('DB_PASS', 'root'); // MAMPのデフォルトパスワード
define('DB_CHARSET', 'utf8mb4');

// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

// セッション設定（セキュリティ強化）
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // HTTPSの場合は1に設定
ini_set('session.cookie_samesite', 'Strict');

// セッション開始
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// CSRFトークン生成（セッションに存在しない場合）
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

/**
 * データベース接続を取得
 *
 * @return PDO データベース接続オブジェクト
 * @throws PDOException 接続エラー
 */
function getDB() {
    static $pdo = null;

    if ($pdo === null) {
        try {
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=%s',
                DB_HOST,
                DB_PORT,
                DB_NAME,
                DB_CHARSET
            );

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];

            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

        } catch (PDOException $e) {
            // 本番環境では詳細なエラーメッセージを隠す
            error_log('Database connection error: ' . $e->getMessage());
            die('データベース接続エラーが発生しました。');
        }
    }

    return $pdo;
}

/**
 * CSRFトークンを検証
 *
 * @param string $token 検証するトークン
 * @return bool 検証結果
 */
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * CSRFトークンを再生成
 */
function regenerateCsrfToken() {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
