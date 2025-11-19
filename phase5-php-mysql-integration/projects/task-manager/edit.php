<?php
/**
 * タスク管理システム - タスク編集
 *
 * セキュリティ対策:
 * - CSRF対策
 * - バリデーション
 * - XSS対策
 * - SQLインジェクション対策
 */

require_once 'functions.php';

$errors = [];
$task = null;

// タスクIDを取得
$task_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($task_id <= 0) {
    setFlashMessage('error', '不正なタスクIDです。');
    redirect('index.php');
}

// タスクを取得
$task = getTaskById($task_id);

if (!$task) {
    setFlashMessage('error', 'タスクが見つかりませんでした。');
    redirect('index.php');
}

// POST リクエストの処理
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF トークン検証
    if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
        $errors[] = '不正なリクエストです。';
    } else {
        // フォームデータを取得
        $form_data = [
            'title' => trim($_POST['title'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'priority' => $_POST['priority'] ?? 'medium',
            'status' => $_POST['status'] ?? 'pending',
            'due_date' => $_POST['due_date'] ?? ''
        ];

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

        // エラーがなければタスクを更新
        if (empty($errors)) {
            if (updateTask($task_id, $form_data)) {
                setFlashMessage('success', 'タスクを更新しました。');
                regenerateCsrfToken();
                redirect('index.php');
            } else {
                $errors[] = 'タスクの更新に失敗しました。';
            }
        } else {
            // エラーがある場合、フォームに入力値を保持
            $task = array_merge($task, $form_data);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>タスク編集 - タスク管理システム</title>
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

        .info-box { background: #e7f3ff; padding: 10px 15px; border-left: 4px solid #007bff; margin-bottom: 20px; }
        .info-box small { color: #666; }
    </style>
</head>
<body>
    <h1>✏️ タスク編集</h1>

    <div class="form-container">
        <!-- タスク情報 -->
        <div class="info-box">
            <strong>タスクID:</strong> <?php echo h($task['id']); ?><br>
            <small>作成日時: <?php echo h($task['created_at']); ?></small><br>
            <small>更新日時: <?php echo h($task['updated_at']); ?></small>
        </div>

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
        <form method="POST" action="edit.php?id=<?php echo h($task['id']); ?>">
            <!-- CSRF トークン -->
            <input type="hidden" name="csrf_token" value="<?php echo h($_SESSION['csrf_token']); ?>">

            <!-- タイトル -->
            <div class="form-group">
                <label for="title">タスク名 <span style="color: red;">*</span></label>
                <input type="text" id="title" name="title" value="<?php echo h($task['title']); ?>" required>
            </div>

            <!-- 説明 -->
            <div class="form-group">
                <label for="description">説明</label>
                <textarea id="description" name="description"><?php echo h($task['description'] ?? ''); ?></textarea>
            </div>

            <!-- 優先度 -->
            <div class="form-group">
                <label for="priority">優先度</label>
                <select id="priority" name="priority">
                    <option value="low" <?php echo $task['priority'] === 'low' ? 'selected' : ''; ?>>低</option>
                    <option value="medium" <?php echo $task['priority'] === 'medium' ? 'selected' : ''; ?>>中</option>
                    <option value="high" <?php echo $task['priority'] === 'high' ? 'selected' : ''; ?>>高</option>
                </select>
            </div>

            <!-- ステータス -->
            <div class="form-group">
                <label for="status">ステータス</label>
                <select id="status" name="status">
                    <option value="pending" <?php echo $task['status'] === 'pending' ? 'selected' : ''; ?>>未完了</option>
                    <option value="completed" <?php echo $task['status'] === 'completed' ? 'selected' : ''; ?>>完了</option>
                </select>
            </div>

            <!-- 期限 -->
            <div class="form-group">
                <label for="due_date">期限日</label>
                <input type="date" id="due_date" name="due_date" value="<?php echo h($task['due_date'] ?? ''); ?>">
            </div>

            <!-- ボタン -->
            <div class="form-actions">
                <button type="submit" class="btn">💾 更新</button>
                <a href="index.php" class="btn btn-secondary">← 戻る</a>
            </div>
        </form>
    </div>
</body>
</html>
