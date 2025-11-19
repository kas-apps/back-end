<?php
/**
 * タスク管理システム - 共通関数
 *
 * すべてのセキュリティ対策と再利用可能な関数を定義
 */

require_once 'config.php';

/**
 * XSS対策：HTML出力用エスケープ
 *
 * @param string $str エスケープする文字列
 * @return string エスケープされた文字列
 */
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

/**
 * バリデーション：タスクタイトル
 *
 * @param string $title タイトル
 * @return array エラーメッセージの配列
 */
function validateTitle($title) {
    $errors = [];

    if (empty($title)) {
        $errors[] = 'タイトルを入力してください';
    } elseif (strlen($title) > 255) {
        $errors[] = 'タイトルは255文字以内で入力してください';
    }

    return $errors;
}

/**
 * バリデーション：優先度
 *
 * @param string $priority 優先度
 * @return bool 有効な値か
 */
function validatePriority($priority) {
    $allowed = ['low', 'medium', 'high'];
    return in_array($priority, $allowed, true);
}

/**
 * バリデーション：ステータス
 *
 * @param string $status ステータス
 * @return bool 有効な値か
 */
function validateStatus($status) {
    $allowed = ['pending', 'completed'];
    return in_array($status, $allowed, true);
}

/**
 * バリデーション：日付
 *
 * @param string $date 日付（YYYY-MM-DD形式）
 * @return bool 有効な日付か
 */
function validateDate($date) {
    if (empty($date)) {
        return true; // 空は許可（任意項目）
    }

    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

/**
 * タスク一覧を取得（フィルタ・ページング対応）
 *
 * @param array $filters フィルタ条件
 * @param int $page ページ番号
 * @param int $per_page 1ページあたりの件数
 * @return array タスク一覧と総件数
 */
function getTasks($filters = [], $page = 1, $per_page = 10) {
    $pdo = getDB();

    // WHERE句の構築
    $where_conditions = [];
    $bind_params = [];

    // ステータスフィルタ
    if (isset($filters['status']) && validateStatus($filters['status'])) {
        $where_conditions[] = 'status = :status';
        $bind_params[':status'] = $filters['status'];
    }

    // 優先度フィルタ
    if (isset($filters['priority']) && validatePriority($filters['priority'])) {
        $where_conditions[] = 'priority = :priority';
        $bind_params[':priority'] = $filters['priority'];
    }

    // 検索キーワード
    if (!empty($filters['keyword'])) {
        $where_conditions[] = '(title LIKE :keyword OR description LIKE :keyword)';
        $bind_params[':keyword'] = '%' . $filters['keyword'] . '%';
    }

    $where_clause = !empty($where_conditions) ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

    // 総件数を取得
    $count_sql = "SELECT COUNT(*) FROM tasks {$where_clause}";
    $count_stmt = $pdo->prepare($count_sql);
    foreach ($bind_params as $key => $value) {
        $count_stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    $count_stmt->execute();
    $total_count = (int)$count_stmt->fetchColumn();

    // ページング計算
    $page = max(1, (int)$page);
    $per_page = max(1, min(100, (int)$per_page));
    $offset = ($page - 1) * $per_page;
    $total_pages = ceil($total_count / $per_page);

    // タスクを取得
    $sql = "SELECT * FROM tasks {$where_clause} ORDER BY
            CASE priority
                WHEN 'high' THEN 1
                WHEN 'medium' THEN 2
                WHEN 'low' THEN 3
            END,
            due_date ASC,
            created_at DESC
            LIMIT :limit OFFSET :offset";

    $stmt = $pdo->prepare($sql);
    foreach ($bind_params as $key => $value) {
        $stmt->bindValue($key, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
    }
    $stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return [
        'tasks' => $tasks,
        'total_count' => $total_count,
        'total_pages' => $total_pages,
        'current_page' => $page
    ];
}

/**
 * タスクをIDで取得
 *
 * @param int $id タスクID
 * @return array|null タスクデータ
 */
function getTaskById($id) {
    $pdo = getDB();

    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * タスクを追加
 *
 * @param array $data タスクデータ
 * @return int|false 挿入されたタスクのID、失敗時はfalse
 */
function createTask($data) {
    $pdo = getDB();

    try {
        $sql = "INSERT INTO tasks (title, description, priority, status, due_date)
                VALUES (:title, :description, :priority, :status, :due_date)";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':title', $data['title'], PDO::PARAM_STR);
        $stmt->bindValue(':description', $data['description'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':priority', $data['priority'] ?? 'medium', PDO::PARAM_STR);
        $stmt->bindValue(':status', $data['status'] ?? 'pending', PDO::PARAM_STR);
        $stmt->bindValue(':due_date', !empty($data['due_date']) ? $data['due_date'] : null, PDO::PARAM_STR);

        $stmt->execute();
        return $pdo->lastInsertId();

    } catch (PDOException $e) {
        error_log('Create task error: ' . $e->getMessage());
        return false;
    }
}

/**
 * タスクを更新
 *
 * @param int $id タスクID
 * @param array $data 更新するデータ
 * @return bool 成功時true
 */
function updateTask($id, $data) {
    $pdo = getDB();

    try {
        $sql = "UPDATE tasks SET
                title = :title,
                description = :description,
                priority = :priority,
                status = :status,
                due_date = :due_date
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->bindValue(':title', $data['title'], PDO::PARAM_STR);
        $stmt->bindValue(':description', $data['description'] ?? null, PDO::PARAM_STR);
        $stmt->bindValue(':priority', $data['priority'] ?? 'medium', PDO::PARAM_STR);
        $stmt->bindValue(':status', $data['status'] ?? 'pending', PDO::PARAM_STR);
        $stmt->bindValue(':due_date', !empty($data['due_date']) ? $data['due_date'] : null, PDO::PARAM_STR);

        return $stmt->execute();

    } catch (PDOException $e) {
        error_log('Update task error: ' . $e->getMessage());
        return false;
    }
}

/**
 * タスクを削除
 *
 * @param int $id タスクID
 * @return bool 成功時true
 */
function deleteTask($id) {
    $pdo = getDB();

    try {
        $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();

    } catch (PDOException $e) {
        error_log('Delete task error: ' . $e->getMessage());
        return false;
    }
}

/**
 * タスクのステータスを切り替え
 *
 * @param int $id タスクID
 * @return bool 成功時true
 */
function toggleTaskStatus($id) {
    $pdo = getDB();

    try {
        $sql = "UPDATE tasks SET status =
                CASE
                    WHEN status = 'pending' THEN 'completed'
                    WHEN status = 'completed' THEN 'pending'
                END
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();

    } catch (PDOException $e) {
        error_log('Toggle task status error: ' . $e->getMessage());
        return false;
    }
}

/**
 * 優先度のラベルを取得
 *
 * @param string $priority 優先度
 * @return string ラベル
 */
function getPriorityLabel($priority) {
    $labels = [
        'low' => '低',
        'medium' => '中',
        'high' => '高'
    ];
    return $labels[$priority] ?? '不明';
}

/**
 * ステータスのラベルを取得
 *
 * @param string $status ステータス
 * @return string ラベル
 */
function getStatusLabel($status) {
    $labels = [
        'pending' => '未完了',
        'completed' => '完了'
    ];
    return $labels[$status] ?? '不明';
}

/**
 * 期限までの残り日数を取得
 *
 * @param string|null $due_date 期限日
 * @return int|null 残り日数（負の値は期限切れ）
 */
function getDaysRemaining($due_date) {
    if (empty($due_date)) {
        return null;
    }

    $now = new DateTime();
    $due = new DateTime($due_date);
    $diff = $now->diff($due);

    return $diff->invert ? -$diff->days : $diff->days;
}

/**
 * リダイレクト
 *
 * @param string $url リダイレクト先URL
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * フラッシュメッセージを設定
 *
 * @param string $type メッセージタイプ（success, error, info）
 * @param string $message メッセージ内容
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type,
        'message' => $message
    ];
}

/**
 * フラッシュメッセージを取得して削除
 *
 * @return array|null フラッシュメッセージ
 */
function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $message;
    }
    return null;
}
