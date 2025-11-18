# Lesson 01: データベース接続 🔌

**学習目標**：PHPからMySQLに安全に接続し、接続エラーを適切に処理できるようになる！

## なぜデータベース接続を学ぶの？

PHPからMySQLに接続して、動的なWebアプリケーションを作るため！

## PDOを使った接続

```php
<?php
$host = 'localhost';
$port = '8889';
$dbname = 'mydb';
$username = 'root';
$password = 'root';

$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    echo "接続成功！";
} catch (PDOException $e) {
    die("接続エラー: " . $e->getMessage());
}
?>
```

## 設定ファイルの分離

config.phpに接続情報をまとめる！

👉 **[演習問題を見る](exercises/README.md)**

**Let's vibe and code! 🎉**
