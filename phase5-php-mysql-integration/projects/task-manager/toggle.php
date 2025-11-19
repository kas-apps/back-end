<?php
/**
 * タスク管理システム - ステータス切り替え
 *
 * セキュリティ対策:
 * - CSRF対策
 * - POSTメソッドのみ受け付け
 * - タスク存在確認
 */

require_once 'functions.php';

// POSTリクエストのみ受け付け
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    setFlashMessage('error', '不正なアクセスです。');
    redirect('index.php');
}

// CSRFトークン検証
if (!isset($_POST['csrf_token']) || !verifyCsrfToken($_POST['csrf_token'])) {
    setFlashMessage('error', '不正なリクエストです。');
    redirect('index.php');
}

// タスクIDを取得
$task_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

if ($task_id <= 0) {
    setFlashMessage('error', '不正なタスクIDです。');
    redirect('index.php');
}

// タスクが存在するか確認
$task = getTaskById($task_id);

if (!$task) {
    setFlashMessage('error', 'タスクが見つかりませんでした。');
    redirect('index.php');
}

// ステータスを切り替え
if (toggleTaskStatus($task_id)) {
    $new_status = $task['status'] === 'pending' ? '完了' : '未完了';
    setFlashMessage('success', "タスクを「{$new_status}」に変更しました。");
    regenerateCsrfToken();
} else {
    setFlashMessage('error', 'ステータスの変更に失敗しました。');
}

redirect('index.php');
