# 総合プロジェクト2: タスク管理システム ✅

**プロジェクト概要**：シンプルだけど実用的なタスク管理システム（ToDoリスト）を構築する！

## 機能一覧

- タスク追加（タイトル、詳細、期限、優先度）
- タスク一覧（ページング）
- タスク編集
- タスク削除（確認画面あり）
- タスク完了/未完了切り替え
- フィルタリング（完了/未完了、期限順、優先度順）

## データベース設計

```sql
CREATE DATABASE IF NOT EXISTS task_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE task_manager;

CREATE TABLE tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    due_date DATE,
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    is_completed BOOLEAN DEFAULT FALSE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

## セキュリティチェックリスト

✅ プリペアドステートメント
✅ htmlspecialchars()
✅ CSRFトークン
✅ バリデーション

**Let's vibe and code! 🎉**
