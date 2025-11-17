-- Lesson 01: データベースとは - サンプルクエリ集
-- このファイルは、Lesson 01で学んだ概念を実践するためのサンプルSQLです

-- ================================================================================
-- ブログシステムの例
-- ================================================================================

-- usersテーブルの作成
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- postsテーブルの作成
CREATE TABLE posts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(200) NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- サンプルデータの挿入
INSERT INTO users (name, email) VALUES
('太郎', 'taro@example.com'),
('花子', 'hanako@example.com'),
('次郎', 'jiro@example.com');

INSERT INTO posts (user_id, title, content) VALUES
(1, '初めての投稿', 'こんにちは！太郎です。ブログを始めました！'),
(1, 'PHP学習中', 'PHPの基礎を学んでいます。楽しいです！'),
(2, '花子の日記', 'よろしくお願いします。'),
(3, 'データベース入門', 'MySQLを勉強し始めました！');

-- ================================================================================
-- ECサイトの商品管理の例
-- ================================================================================

-- categoriesテーブルの作成
CREATE TABLE categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) UNIQUE NOT NULL
);

-- productsテーブルの作成
CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    category_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    stock INT DEFAULT 0,
    FOREIGN KEY (category_id) REFERENCES categories(id)
);

-- サンプルデータの挿入
INSERT INTO categories (name) VALUES
('家電'),
('書籍'),
('食品');

INSERT INTO products (category_id, name, price, stock) VALUES
(1, 'ノートパソコン', 80000.00, 10),
(1, 'スマートフォン', 60000.00, 15),
(1, 'タブレット', 40000.00, 8),
(2, 'PHP入門書', 3000.00, 20),
(2, 'MySQL完全ガイド', 3500.00, 15),
(3, 'コーヒー豆（200g）', 1500.00, 50);

-- ================================================================================
-- データベース設計の確認用クエリ
-- ================================================================================

-- NOTE: これらのクエリはLesson 03で詳しく学びますが、
-- テーブルが正しく作成されたか確認するために使えます

-- テーブル一覧を表示（MySQL）
SHOW TABLES;

-- usersテーブルの構造を確認
DESCRIBE users;

-- postsテーブルの構造を確認
DESCRIBE posts;

-- categoriesテーブルの構造を確認
DESCRIBE categories;

-- productsテーブルの構造を確認
DESCRIBE products;

-- ================================================================================
-- 主キーと外部キーの関係を確認
-- ================================================================================

-- 全ユーザーを表示
SELECT * FROM users;

-- 全投稿を表示
SELECT * FROM posts;

-- 特定のユーザー（太郎さん）の投稿を確認
-- user_id = 1の投稿を取得
SELECT * FROM posts WHERE user_id = 1;

-- 全カテゴリを表示
SELECT * FROM categories;

-- 全商品を表示
SELECT * FROM products;

-- 特定のカテゴリ（家電）の商品を確認
-- category_id = 1の商品を取得
SELECT * FROM products WHERE category_id = 1;

-- ================================================================================
-- クリーンアップ（テーブルを削除）
-- ================================================================================

-- NOTE: テーブルを削除する際は、外部キー制約があるため、
-- 参照される側（親テーブル）より先に、参照する側（子テーブル）を削除する必要があります

-- ブログシステムのテーブルを削除
DROP TABLE IF EXISTS posts;  -- 先に子テーブル（外部キーを持つ側）
DROP TABLE IF EXISTS users;  -- 後で親テーブル（参照される側）

-- ECサイトのテーブルを削除
DROP TABLE IF EXISTS products;  -- 先に子テーブル（外部キーを持つ側）
DROP TABLE IF EXISTS categories;  -- 後で親テーブル（参照される側）
