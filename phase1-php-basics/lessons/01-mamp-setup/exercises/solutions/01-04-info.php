<?php
// システム情報を表示するプログラム

// 各種情報を取得
$name = "山田太郎";
$today = date('Y年m月d日');
$phpVersion = phpversion();
$serverInfo = $_SERVER['SERVER_SOFTWARE'];

// 表示（HTMLで見やすく）
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <title>システム情報</title>
</head>
<body>
    <h1>=== システム情報 ===</h1>
    <p>名前：<?php echo $name; ?></p>
    <p>日付：<?php echo $today; ?></p>
    <p>PHPバージョン：<?php echo $phpVersion; ?></p>
    <p>Webサーバー：<?php echo $serverInfo; ?></p>
</body>
</html>
