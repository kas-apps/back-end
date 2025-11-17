-- ============================================
-- Phase 3 Lesson 03: SQL基本操作（CRUD）
-- データベースとテーブルの作成SQL
-- ============================================

-- データベースの作成
CREATE DATABASE IF NOT EXISTS sql_basics_db CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

-- データベースを選択
USE sql_basics_db;

-- booksテーブルの作成
CREATE TABLE IF NOT EXISTS books (
    id INT AUTO_INCREMENT PRIMARY KEY COMMENT '書籍ID',
    title VARCHAR(200) NOT NULL COMMENT 'タイトル',
    author VARCHAR(100) NOT NULL COMMENT '著者',
    price INT NOT NULL COMMENT '価格',
    stock INT NOT NULL DEFAULT 0 COMMENT '在庫数',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '登録日時'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='書籍テーブル';

-- テーブルが作成されたことを確認
SELECT 'データベースとテーブルの作成が完了しました！' AS message;
SHOW TABLES;
