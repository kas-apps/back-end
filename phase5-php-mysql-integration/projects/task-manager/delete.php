<?php
/**
 * タスク管理システム - タスク削除
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

// タスクを削除
if (deleteTask($task_id)) {
    setFlashMessage('success', 'タスクを削除しました。');
    regenerateCsrfToken();
} else {
    setFlashMessage('error', 'タスクの削除に失敗しました。');
}

redirect('index.php');
