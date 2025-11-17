<?php
// 自己紹介ページ

// 個人情報を変数に入れる
$name = "山田太郎";
$age = 25;
$hobbies = ["プログラミング", "音楽", "旅行"];
$currentDateTime = date('Y年m月d日 H:i:s');
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>自己紹介</title>
    <style>
        body {
            font-family: 'Hiragino Sans', 'メイリオ', sans-serif;
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f0f8ff;
        }
        .container {
            background-color: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
        }
        .info {
            margin: 20px 0;
        }
        .label {
            font-weight: bold;
            color: #34495e;
        }
        ul {
            list-style-type: none;
            padding-left: 20px;
        }
        ul li:before {
            content: "・";
            color: #3498db;
            font-weight: bold;
            margin-right: 5px;
        }
        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ecf0f1;
            color: #7f8c8d;
            font-size: 0.9em;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>自己紹介</h1>

        <div class="info">
            <p><span class="label">名前：</span><?php echo $name; ?></p>
            <p><span class="label">年齢：</span><?php echo $age; ?>歳</p>
        </div>

        <div class="info">
            <p class="label">好きなもの：</p>
            <ul>
                <?php foreach ($hobbies as $hobby): ?>
                    <li><?php echo $hobby; ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="footer">
            <p>今日は<?php echo $currentDateTime; ?>です。</p>
            <p>私はPHPを学んでいます！</p>
        </div>
    </div>
</body>
</html>
