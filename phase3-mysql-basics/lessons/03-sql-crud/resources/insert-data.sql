-- ============================================
-- Phase 3 Lesson 03: SQL基本操作（CRUD）
-- サンプルデータの挿入SQL
-- ============================================

-- データベースを選択
USE sql_basics_db;

-- 既存のデータをクリア（演習をやり直す場合）
TRUNCATE TABLE books;

-- booksテーブルにサンプルデータを挿入
INSERT INTO books (title, author, price, stock) VALUES
('PHP入門', '山田太郎', 3000, 15),
('Python基礎', '佐藤花子', 2800, 12),
('データベース設計', '鈴木一郎', 3500, 10),
('Web開発の基礎', '田中次郎', 2500, 20),
('JavaScript実践', '高橋美咲', 3200, 8);

-- データが挿入されたことを確認
SELECT 'サンプルデータの挿入が完了しました！' AS message;
SELECT * FROM books;
