# Lesson 01: 解答例

## 問題1-1

```php
<?php
$host = 'localhost';
$port = '8889';
$dbname = 'mydb';
$username = 'root';
$password = 'root';

$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
$pdo = new PDO($dsn, $username, $password);
echo "接続成功！";
?>
```

## 問題1-2

```php
<?php
try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
    echo "接続成功！";
} catch (PDOException $e) {
    echo "接続エラー: " . $e->getMessage();
}
?>
```
