<?php
/**
 * タスク管理システム - メイン画面
 *
 * 機能:
 * - タスク一覧表示
 * - フィルタリング（ステータス、優先度、キーワード検索）
 * - ページング
 * - 完了/未完了の切り替え
 */

require_once 'functions.php';

// フィルタ条件を取得
$filters = [];

if (isset($_GET['status']) && validateStatus($_GET['status'])) {
    $filters['status'] = $_GET['status'];
}

if (isset($_GET['priority']) && validatePriority($_GET['priority'])) {
    $filters['priority'] = $_GET['priority'];
}

if (!empty($_GET['keyword'])) {
    $filters['keyword'] = trim($_GET['keyword']);
}

// ページ番号を取得
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// タスクを取得
$result = getTasks($filters, $page, 10);
$tasks = $result['tasks'];
$total_count = $result['total_count'];
$total_pages = $result['total_pages'];
$current_page = $result['current_page'];

// フラッシュメッセージを取得
$flash = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>タスク管理システム</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: Arial, sans-serif; line-height: 1.6; padding: 20px; max-width: 1200px; margin: 0 auto; background: #f5f5f5; }
        h1 { margin-bottom: 20px; color: #333; }

        /* フラッシュメッセージ */
        .flash { padding: 10px 15px; margin-bottom: 20px; border-radius: 4px; }
        .flash.success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .flash.error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .flash.info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }

        /* ヘッダー */
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
        .btn { padding: 10px 20px; background: #007bff; color: #fff; text-decoration: none; border-radius: 4px; border: none; cursor: pointer; display: inline-block; }
        .btn:hover { background: #0056b3; }
        .btn-sm { padding: 5px 10px; font-size: 0.9em; }
        .btn-danger { background: #dc3545; }
        .btn-danger:hover { background: #c82333; }
        .btn-success { background: #28a745; }
        .btn-success:hover { background: #218838; }

        /* フィルタ */
        .filters { background: #fff; padding: 15px; margin-bottom: 20px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .filters form { display: flex; gap: 10px; flex-wrap: wrap; }
        .filters input, .filters select { padding: 8px; border: 1px solid #ddd; border-radius: 4px; }
        .filters input[type="text"] { flex: 1; min-width: 200px; }

        /* タスク統計 */
        .stats { background: #fff; padding: 15px; margin-bottom: 20px; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }

        /* タスクリスト */
        .task-list { background: #fff; border-radius: 4px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .task-item { padding: 15px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: flex-start; }
        .task-item:last-child { border-bottom: none; }
        .task-item.completed { background: #f9f9f9; }
        .task-item.completed .task-title { text-decoration: line-through; color: #999; }

        .task-info { flex: 1; }
        .task-title { font-weight: bold; margin-bottom: 5px; font-size: 1.1em; }
        .task-description { color: #666; margin-bottom: 8px; }
        .task-meta { display: flex; gap: 15px; font-size: 0.9em; color: #666; }
        .task-meta span { display: inline-block; }

        .priority-high { color: #dc3545; font-weight: bold; }
        .priority-medium { color: #ffc107; font-weight: bold; }
        .priority-low { color: #28a745; font-weight: bold; }

        .due-overdue { color: #dc3545; font-weight: bold; }
        .due-soon { color: #ffc107; }

        .task-actions { display: flex; gap: 5px; }

        /* ページング */
        .pagination { display: flex; justify-content: center; gap: 5px; margin-top: 20px; padding: 15px; background: #fff; border-radius: 4px; }
        .pagination a, .pagination span { padding: 8px 12px; border: 1px solid #ddd; border-radius: 4px; text-decoration: none; color: #333; }
        .pagination a:hover { background: #007bff; color: #fff; border-color: #007bff; }
        .pagination .current { background: #007bff; color: #fff; border-color: #007bff; font-weight: bold; }
        .pagination .disabled { background: #f0f0f0; color: #999; }

        /* 空状態 */
        .empty-state { text-align: center; padding: 40px; color: #666; }
    </style>
</head>
<body>
    <h1>📋 タスク管理システム</h1>

    <!-- フラッシュメッセージ -->
    <?php if ($flash): ?>
        <div class="flash <?php echo h($flash['type']); ?>">
            <?php echo h($flash['message']); ?>
        </div>
    <?php endif; ?>

    <!-- ヘッダー -->
    <div class="header">
        <div>
            <a href="add.php" class="btn">➕ 新規タスク追加</a>
        </div>
        <div>
            <a href="index.php" class="btn">🔄 フィルタをクリア</a>
        </div>
    </div>

    <!-- フィルタ -->
    <div class="filters">
        <form method="GET" action="index.php">
            <input type="text" name="keyword" placeholder="キーワード検索..."
                   value="<?php echo h($_GET['keyword'] ?? ''); ?>">

            <select name="status">
                <option value="">すべてのステータス</option>
                <option value="pending" <?php echo (isset($filters['status']) && $filters['status'] === 'pending') ? 'selected' : ''; ?>>未完了</option>
                <option value="completed" <?php echo (isset($filters['status']) && $filters['status'] === 'completed') ? 'selected' : ''; ?>>完了</option>
            </select>

            <select name="priority">
                <option value="">すべての優先度</option>
                <option value="high" <?php echo (isset($filters['priority']) && $filters['priority'] === 'high') ? 'selected' : ''; ?>>高</option>
                <option value="medium" <?php echo (isset($filters['priority']) && $filters['priority'] === 'medium') ? 'selected' : ''; ?>>中</option>
                <option value="low" <?php echo (isset($filters['priority']) && $filters['priority'] === 'low') ? 'selected' : ''; ?>>低</option>
            </select>

            <button type="submit" class="btn">🔍 検索</button>
        </form>
    </div>

    <!-- 統計 -->
    <div class="stats">
        <strong>検索結果:</strong> 全 <?php echo number_format($total_count); ?> 件のタスク
        <?php if ($total_pages > 1): ?>
            (<?php echo number_format($total_pages); ?> ページ中 <?php echo number_format($current_page); ?> ページ目)
        <?php endif; ?>
    </div>

    <!-- タスクリスト -->
    <?php if (empty($tasks)): ?>
        <div class="empty-state">
            <p>タスクが見つかりませんでした。</p>
            <p><a href="add.php">新しいタスクを追加</a>してみましょう！</p>
        </div>
    <?php else: ?>
        <div class="task-list">
            <?php foreach ($tasks as $task): ?>
                <?php
                $days_remaining = getDaysRemaining($task['due_date']);
                $due_class = '';
                if ($days_remaining !== null) {
                    if ($days_remaining < 0) {
                        $due_class = 'due-overdue';
                    } elseif ($days_remaining <= 3) {
                        $due_class = 'due-soon';
                    }
                }
                ?>
                <div class="task-item <?php echo $task['status'] === 'completed' ? 'completed' : ''; ?>">
                    <div class="task-info">
                        <div class="task-title"><?php echo h($task['title']); ?></div>

                        <?php if (!empty($task['description'])): ?>
                            <div class="task-description"><?php echo h($task['description']); ?></div>
                        <?php endif; ?>

                        <div class="task-meta">
                            <span class="priority-<?php echo h($task['priority']); ?>">
                                優先度: <?php echo h(getPriorityLabel($task['priority'])); ?>
                            </span>
                            <span>
                                ステータス: <?php echo h(getStatusLabel($task['status'])); ?>
                            </span>
                            <?php if ($task['due_date']): ?>
                                <span class="<?php echo $due_class; ?>">
                                    期限: <?php echo h($task['due_date']); ?>
                                    <?php if ($days_remaining !== null): ?>
                                        (<?php echo $days_remaining >= 0 ? '残り' . $days_remaining . '日' : abs($days_remaining) . '日超過'; ?>)
                                    <?php endif; ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="task-actions">
                        <!-- 完了/未完了切り替え -->
                        <form method="POST" action="toggle.php" style="display:inline;">
                            <input type="hidden" name="csrf_token" value="<?php echo h($_SESSION['csrf_token']); ?>">
                            <input type="hidden" name="id" value="<?php echo h($task['id']); ?>">
                            <button type="submit" class="btn btn-sm <?php echo $task['status'] === 'completed' ? 'btn-danger' : 'btn-success'; ?>">
                                <?php echo $task['status'] === 'completed' ? '↩️ 未完了に戻す' : '✅ 完了'; ?>
                            </button>
                        </form>

                        <!-- 編集 -->
                        <a href="edit.php?id=<?php echo h($task['id']); ?>" class="btn btn-sm">✏️ 編集</a>

                        <!-- 削除 -->
                        <form method="POST" action="delete.php" style="display:inline;"
                              onsubmit="return confirm('本当に削除しますか？');">
                            <input type="hidden" name="csrf_token" value="<?php echo h($_SESSION['csrf_token']); ?>">
                            <input type="hidden" name="id" value="<?php echo h($task['id']); ?>">
                            <button type="submit" class="btn btn-sm btn-danger">🗑️ 削除</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- ページング -->
        <?php if ($total_pages > 1): ?>
            <div class="pagination">
                <!-- 前へ -->
                <?php if ($current_page > 1): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page - 1])); ?>">« 前へ</a>
                <?php else: ?>
                    <span class="disabled">« 前へ</span>
                <?php endif; ?>

                <!-- ページ番号 -->
                <?php
                $range = 2;
                $start = max(1, $current_page - $range);
                $end = min($total_pages, $current_page + $range);

                if ($start > 1) {
                    echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => 1])) . '">1</a>';
                    if ($start > 2) {
                        echo '<span>...</span>';
                    }
                }

                for ($i = $start; $i <= $end; $i++) {
                    if ($i === $current_page) {
                        echo '<span class="current">' . $i . '</span>';
                    } else {
                        echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $i])) . '">' . $i . '</a>';
                    }
                }

                if ($end < $total_pages) {
                    if ($end < $total_pages - 1) {
                        echo '<span>...</span>';
                    }
                    echo '<a href="?' . http_build_query(array_merge($_GET, ['page' => $total_pages])) . '">' . $total_pages . '</a>';
                }
                ?>

                <!-- 次へ -->
                <?php if ($current_page < $total_pages): ?>
                    <a href="?<?php echo http_build_query(array_merge($_GET, ['page' => $current_page + 1])); ?>">次へ »</a>
                <?php else: ?>
                    <span class="disabled">次へ »</span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</body>
</html>
