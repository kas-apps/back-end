<?php
// データベース接続設定（MAMP環境）

// 接続情報
define('DB_HOST', 'localhost');
define('DB_PORT', '8889');  // MAMPのデフォルトポート
define('DB_NAME', 'mydb');  // データベース名
define('DB_USER', 'root');  // MAMPのデフォルトユーザー名
define('DB_PASS', 'root');  // MAMPのデフォルトパスワード

// DSN（Data Source Name）
$dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';

// PDOで接続
try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("データベース接続エラーが発生しました。");
}
?>
