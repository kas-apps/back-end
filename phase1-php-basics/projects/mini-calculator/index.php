<?php
// calculator.phpを読み込む
require_once 'calculator.php';

// 結果とエラーメッセージを初期化
$result = null;
$error = null;

// フォームが送信されたか確認
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // フォームデータを取得
    $num1 = $_POST['num1'] ?? '';
    $num2 = $_POST['num2'] ?? '';
    $operator = $_POST['operator'] ?? '';

    // バリデーション
    $error = validateInput($num1, $num2, $operator);

    // エラーがなければ計算
    if ($error === null) {
        $result = calculate((float)$num1, (float)$num2, $operator);
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ミニ電卓 - Phase 1 プロジェクト</title>
    <style>
        body {
            font-family: 'Hiragino Sans', 'メイリオ', sans-serif;
            max-width: 500px;
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
            text-align: center;
            border-bottom: 3px solid #3498db;
            padding-bottom: 10px;
        }
        .form-group {
            margin: 15px 0;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #34495e;
        }
        input, select {
            width: 100%;
            padding: 10px;
            border: 2px solid #ecf0f1;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            margin-top: 10px;
        }
        button:hover {
            background-color: #2980b9;
        }
        .result {
            margin-top: 20px;
            padding: 20px;
            background-color: #d4edda;
            border: 2px solid #c3e6cb;
            border-radius: 5px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: #155724;
        }
        .error {
            margin-top: 20px;
            padding: 15px;
            background-color: #f8d7da;
            border: 2px solid #f5c6cb;
            border-radius: 5px;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧮 ミニ電卓</h1>
        <p style="text-align: center; color: #7f8c8d;">Phase 1の集大成プロジェクト！</p>

        <form method="POST">
            <div class="form-group">
                <label for="num1">数値1：</label>
                <input
                    type="text"
                    id="num1"
                    name="num1"
                    value="<?php echo isset($_POST['num1']) ? htmlspecialchars($_POST['num1'], ENT_QUOTES, 'UTF-8') : ''; ?>"
                    placeholder="例: 10"
                >
            </div>

            <div class="form-group">
                <label for="operator">演算子：</label>
                <select id="operator" name="operator">
                    <option value="+" <?php echo (isset($_POST['operator']) && $_POST['operator'] === '+') ? 'selected' : ''; ?>>+ (足し算)</option>
                    <option value="-" <?php echo (isset($_POST['operator']) && $_POST['operator'] === '-') ? 'selected' : ''; ?>>- (引き算)</option>
                    <option value="*" <?php echo (isset($_POST['operator']) && $_POST['operator'] === '*') ? 'selected' : ''; ?>>× (掛け算)</option>
                    <option value="/" <?php echo (isset($_POST['operator']) && $_POST['operator'] === '/') ? 'selected' : ''; ?>>÷ (割り算)</option>
                </select>
            </div>

            <div class="form-group">
                <label for="num2">数値2：</label>
                <input
                    type="text"
                    id="num2"
                    name="num2"
                    value="<?php echo isset($_POST['num2']) ? htmlspecialchars($_POST['num2'], ENT_QUOTES, 'UTF-8') : ''; ?>"
                    placeholder="例: 5"
                >
            </div>

            <button type="submit">計算する</button>
        </form>

        <?php if ($error !== null): ?>
            <div class="error">
                ⚠️ エラー：<?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <?php if ($result !== null): ?>
            <div class="result">
                結果：<?php echo htmlspecialchars($result, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
