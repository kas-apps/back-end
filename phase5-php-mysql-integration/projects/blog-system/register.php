<?php
/**
 * ブログシステム - ユーザー登録
 *
 * セキュリティ対策:
 * - パスワードハッシュ化（password_hash）
 * - CSRF対策
 * - バリデーション
 */

require_once 'functions.php';

// 既にログインしている場合はトップページへリダイレクト
if (isLoggedIn()) {
    redirect('index.php');
}

$errors = [];
$success_message = '';
$form_data = [
    'name' => '',
    'email' => '',
];

// POSTリクエスト処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRFトークン検証
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        $errors[] = '不正なリクエストです。';
    } else {
        // フォームデータを取得
        $form_data['name'] = trim($_POST['name'] ?? '');
        $form_data['email'] = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $password_confirm = $_POST['password_confirm'] ?? '';

        // ユーザー登録
        $result = registerUser([
            'name' => $form_data['name'],
            'email' => $form_data['email'],
            'password' => $password,
            'password_confirm' => $password_confirm
        ]);

        if ($result['success']) {
            $success_message = $result['message'];
            regenerateCsrfToken();

            // 成功メッセージを表示後、ログインページへリダイレクト
            header('Refresh: 3; URL=login.php');
        } else {
            $errors[] = $result['message'];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ユーザー登録 - ブログシステム</title>
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
        input[type="text"],
        input[type="email"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
        }
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #4CAF50;
        }
        .error-messages {
            background-color: #ffebee;
            color: #c62828;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #c62828;
        }
        .success-message {
            background-color: #e8f5e9;
            color: #2e7d32;
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
            border-left: 4px solid #2e7d32;
        }
        .btn {
            width: 100%;
            padding: 12px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            font-weight: bold;
        }
        .btn:hover {
            background-color: #45a049;
        }
        .links {
            text-align: center;
            margin-top: 20px;
        }
        .links a {
            color: #4CAF50;
            text-decoration: none;
        }
        .links a:hover {
            text-decoration: underline;
        }
        .help-text {
            font-size: 12px;
            color: #666;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📝 ユーザー登録</h1>

        <?php if (!empty($errors)): ?>
            <div class="error-messages">
                <?php foreach ($errors as $error): ?>
                    <?php echo h($error); ?><br>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="success-message">
                <?php echo h($success_message); ?><br>
                <small>3秒後にログインページへ移動します...</small>
            </div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="csrf_token" value="<?php echo h($_SESSION['csrf_token']); ?>">

            <div class="form-group">
                <label for="name">名前 <span style="color: red;">*</span></label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="<?php echo h($form_data['name']); ?>"
                    required
                    maxlength="100"
                >
            </div>

            <div class="form-group">
                <label for="email">メールアドレス <span style="color: red;">*</span></label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="<?php echo h($form_data['email']); ?>"
                    required
                    maxlength="255"
                >
            </div>

            <div class="form-group">
                <label for="password">パスワード <span style="color: red;">*</span></label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    minlength="8"
                >
                <div class="help-text">8文字以上で入力してください</div>
            </div>

            <div class="form-group">
                <label for="password_confirm">パスワード（確認） <span style="color: red;">*</span></label>
                <input
                    type="password"
                    id="password_confirm"
                    name="password_confirm"
                    required
                    minlength="8"
                >
            </div>

            <button type="submit" class="btn">✅ 登録</button>
        </form>

        <div class="links">
            <a href="login.php">既にアカウントをお持ちの方はこちら</a><br>
            <a href="index.php">← トップページへ戻る</a>
        </div>
    </div>
</body>
</html>
