<?php
/**
 * ブログシステム - 設定ファイル
 *
 * データベース接続、セキュリティ設定、CSRF対策を提供
 */

// エラー表示設定（本番環境では0に設定）
ini_set('display_errors', 1);
error_reporting(E_ALL);

// セッション設定（セキュリティ強化）
ini_set('session.cookie_httponly', 1);  // JavaScriptからのアクセスを防ぐ
ini_set('session.use_only_cookies', 1); // URLでのセッションID送信を禁止
ini_set('session.cookie_samesite', 'Strict'); // CSRF追加防御

// セッション開始
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// データベース接続設定
define('DB_HOST', 'localhost');
define('DB_PORT', '8889');  // MAMPのデフォルトポート
define('DB_NAME', 'blog_system');
define('DB_USER', 'root');
define('DB_PASS', 'root');  // MAMPのデフォルトパスワード
define('DB_CHARSET', 'utf8mb4');

// アプリケーション設定
define('POSTS_PER_PAGE', 10); // 1ページあたりの記事数
define('EXCERPT_LENGTH', 150); // 抜粋の長さ（文字数）

/**
 * データベース接続を取得（シングルトンパターン）
 *
 * @return PDO データベース接続オブジェクト
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
                PDO::ATTR_EMULATE_PREPARES => false,  // 真のプリペアドステートメントを使用
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
            ];

            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // 本番環境では詳細なエラーメッセージを表示しない
            error_log('Database connection error: ' . $e->getMessage());
            die('データベース接続エラーが発生しました。');
        }
    }

    return $pdo;
}

/**
 * CSRFトークンを生成
 *
 * @return string CSRFトークン
 */
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * CSRFトークンを検証
 *
 * @param string $token 検証するトークン
 * @return bool トークンが有効な場合true
 */
function verifyCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * CSRFトークンを再生成（トークンリプレイ攻撃対策）
 *
 * @return string 新しいCSRFトークン
 */
function regenerateCsrfToken() {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf_token'];
}

// 初回アクセス時にCSRFトークンを生成
generateCsrfToken();
