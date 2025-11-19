-- ===================================
-- タスク管理システム - データベーススキーマ
-- ===================================

-- データベースの作成
CREATE DATABASE IF NOT EXISTS task_manager CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE task_manager;

-- tasksテーブルの作成
CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    status ENUM('pending', 'completed') DEFAULT 'pending',
    due_date DATE,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_priority (priority),
    INDEX idx_due_date (due_date),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- サンプルデータの挿入
INSERT INTO tasks (title, description, priority, status, due_date) VALUES
('Phase 5の学習を完了する', 'PHP+MySQL統合の全レッスンを完了する', 'high', 'pending', DATE_ADD(CURDATE(), INTERVAL 7 DAY)),
('セキュリティ対策を復習する', 'SQLインジェクション、XSS、CSRFの対策を復習', 'high', 'pending', DATE_ADD(CURDATE(), INTERVAL 3 DAY)),
('CRUD操作の練習', '基本的なCRUD操作を復習する', 'medium', 'completed', CURDATE()),
('ページング処理を実装する', '省略表示付きのページネーションを実装', 'medium', 'pending', DATE_ADD(CURDATE(), INTERVAL 5 DAY)),
('プロジェクトのREADMEを書く', 'プロジェクトの使い方を文書化する', 'low', 'pending', DATE_ADD(CURDATE(), INTERVAL 10 DAY)),
('データベース設計を学ぶ', '正規化とインデックスについて学習', 'medium', 'completed', DATE_SUB(CURDATE(), INTERVAL 2 DAY)),
('JOIN操作をマスターする', 'INNER JOIN、LEFT JOINの使い分けを理解', 'medium', 'completed', DATE_SUB(CURDATE(), INTERVAL 1 DAY));

-- テーブルの確認
SHOW TABLES;
DESCRIBE tasks;
SELECT COUNT(*) as task_count FROM tasks;
