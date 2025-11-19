<?php
/**
 * タスク管理システム - タスク追加
 *
 * セキュリティ対策:
 * - CSRF対策
 * - バリデーション
 * - XSS対策
 * - SQLインジェクション対策（functions.phpで実装済み）
 */

require_once 'functions.php';

$errors = [];
$form_data = [
    'title' => '',
    'description' => '',
    'priority' => 'medium',
    'status' => 'pending',
    'due_date' => ''
];

// POST リクエストの処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF トークン検証
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        $errors[] = '不正なリクエストです。';
    } else {
        // フォームデータを取得
        $form_data['title'] = trim($_POST['title'] ?? '');
        $form_data['description'] = trim($_POST['description'] ?? '');
        $form_data['priority'] = $_POST['priority'] ?? 'medium';
        $form_data['status'] = $_POST['status'] ?? 'pending';
        $form_data['due_date'] = $_POST['due_date'] ?? '';

        // バリデーション
        $title_errors = validateTitle($form_data['title']);
        if (!empty($title_errors)) {
            $errors = array_merge($errors, $title_errors);
        }

        if (!validatePriority($form_data['priority'])) {
            $errors[] = '優先度が不正です。';
        }

        if (!validateStatus($form_data['status'])) {
            $errors[] = 'ステータスが不正です。';
        }

        if (!validateDate($form_data['due_date'])) {
            $errors[] = '期限日の形式が不正です。';
        }

        // エラーがなければタスクを作成
        if (empty($errors)) {
            $task_id = createTask($form_data);

            if ($task_id) {
                setFlashMessage('success', 'タスクを追加しました。');
                regenerateCsrfToken(); // CSRFトークンを再生成
                redirect('index.php');
            } else {
                $errors[] = 'タスクの追加に失敗しました。';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>タスク追加 - タスク管理システム</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; max-width: 800px; margin: 0 auto; background: #f5f5f5; }
        h1 { margin-bottom: 20px; color: #333; }

        .form-container { background: #fff; padding: 30px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }

        .errors { background: #f8d7da; color: #721c24; padding: 10px 15px; margin-bottom: 20px; border-radius: 4px; border: 1px solid #f5c6cb; }
        .errors ul { margin-left: 20px; }

        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: bold; color: #333; }
        .form-group input[type="text"],
        .form-group input[type="date"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1em;
        }

        .form-group textarea { min-height: 100px; resize: vertical; font-family: Arial, sans-serif; }
        .form-group small { display: block; margin-top: 5px; color: #666; font-size: 0.9em; }

        .form-actions { display: flex; gap: 10px; margin-top: 30px; }

        .btn { padding: 10px 20px; background: #007bff; color: #fff; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; display: inline-block; font-size: 1em; }
        .btn:hover { background: #0056b3; }
        .btn-secondary { background: #6c757d; }
        .btn-secondary:hover { background: #5a6268; }
    </style>
</head>
<body>
    <h1>➕ 新規タスク追加</h1>

    <div class="form-container">
        <!-- エラー表示 -->
        <?php if (!empty($errors)): ?>
            <div class="errors">
                <strong>エラーがあります:</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo h($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- フォーム -->
        <form method="POST" action="add.php">
            <!-- CSRF トークン -->
            <input type="hidden" name="csrf_token" value="<?php echo h($_SESSION['csrf_token']); ?>">

            <!-- タイトル -->
            <div class="form-group">
                <label for="title">タスク名 <span style="color: red;">*</span></label>
                <input type="text" id="title" name="title" value="<?php echo h($form_data['title']); ?>" required>
                <small>例: Phase 5の学習を完了する</small>
            </div>

            <!-- 説明 -->
            <div class="form-group">
                <label for="description">説明</label>
                <textarea id="description" name="description"><?php echo h($form_data['description']); ?></textarea>
                <small>タスクの詳細や補足情報を入力してください（任意）</small>
            </div>

            <!-- 優先度 -->
            <div class="form-group">
                <label for="priority">優先度</label>
                <select id="priority" name="priority">
                    <option value="low" <?php echo $form_data['priority'] === 'low' ? 'selected' : ''; ?>>低</option>
                    <option value="medium" <?php echo $form_data['priority'] === 'medium' ? 'selected' : ''; ?>>中</option>
                    <option value="high" <?php echo $form_data['priority'] === 'high' ? 'selected' : ''; ?>>高</option>
                </select>
            </div>

            <!-- ステータス -->
            <div class="form-group">
                <label for="status">ステータス</label>
                <select id="status" name="status">
                    <option value="pending" <?php echo $form_data['status'] === 'pending' ? 'selected' : ''; ?>>未完了</option>
                    <option value="completed" <?php echo $form_data['status'] === 'completed' ? 'selected' : ''; ?>>完了</option>
                </select>
            </div>

            <!-- 期限 -->
            <div class="form-group">
                <label for="due_date">期限日</label>
                <input type="date" id="due_date" name="due_date" value="<?php echo h($form_data['due_date']); ?>">
                <small>タスクの期限を設定してください（任意）</small>
            </div>

            <!-- ボタン -->
            <div class="form-actions">
                <button type="submit" class="btn">✅ 追加</button>
                <a href="index.php" class="btn btn-secondary">← 戻る</a>
            </div>
        </form>
    </div>
</body>
</html>
