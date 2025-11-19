<?php
/**
 * ブログシステム - ログイン
 *
 * セキュリティ対策:
 * - パスワード検証（password_verify）
 * - セッション固定化攻撃対策（session_regenerate_id）
 * - CSRF対策
 */

require_once 'functions.php';

// 既にログインしている場合はトップページへリダイレクト
if (isLoggedIn()) {
    redirect('index.php');
}

$error_message = '';
$email = '';

// POSTリクエスト処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRFトークン検証
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        $error_message = '不正なリクエストです。';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        // ログイン処理
        $result = loginUser($email, $password);

        if ($result['success']) {
            regenerateCsrfToken();
            redirect('index.php');
        } else {
            $error_message = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログイン - ブログシステム</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #555;
        }
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #2196F3;
        }
        .error-message {
            background-color: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #c62828;
        }
        .btn {
            width: 100%;
            padding: 12px;
            background-color: #2196F3;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn:hover {
            background-color: #1976D2;
        }
        .links {
            text-align: center;
            margin-top: 20px;
        }
        .links a {
            color: #2196F3;
            text-decoration: none;
        }
        .links a:hover {
            text-decoration: underline;
        }
        .info-box {
            background-color: #e3f2fd;
            border-left: 4px solid #2196F3;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .info-box strong {
            display: block;
            margin-bottom: 10px;
            color: #1565C0;
        }
        .info-box code {
            background-color: #fff;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: monospace;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 ログイン</h1>

        <div class="info-box">
            <strong>💡 テスト用アカウント</strong>
            メール: <code>yamada@example.com</code><br>
            パスワード: <code>password123</code>
        </div>

        <?php if ($error_message): ?>
            <div class="error-message">
                <?php echo h($error_message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo h($_SESSION['csrf_token']); ?>">

            <div class="form-group">
                <label for="email">メールアドレス</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?php echo h($email); ?>"
                    required
                >
            </div>

            <div class="form-group">
                <label for="password">パスワード</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                >
            </div>

            <button type="submit" class="btn">🚀 ログイン</button>
        </form>

        <div class="links">
            <a href="register.php">新規登録はこちら</a><br>
            <a href="index.php">← トップページへ戻る</a>
        </div>
    </div>
</body>
</html>
